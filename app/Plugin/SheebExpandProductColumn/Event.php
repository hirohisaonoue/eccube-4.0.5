<?php

/*
 * Project Name: 商品情報項目追加プラグイン for 4.0
 * Copyright(c) 2018 Kenji Nakanishi. All Rights Reserved.
 *
 * https://www.facebook.com/web.kenji.nakanishi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SheebExpandProductColumn;

use Doctrine\ORM\QueryBuilder;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Csv;
use Eccube\Entity\ExportCsvRow;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\ProductClass;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Plugin\SheebExpandProductColumn\Entity\ColumnValue;
use Plugin\SheebExpandProductColumn\Form\Type\Admin\CheckBoxType;
use Plugin\SheebExpandProductColumn\Repository\ColumnValueRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Event implements EventSubscriberInterface
{
    const SESSION_KEY = 'sheeb_expand_product_column';

    private $cacheColumnValues = [];

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            /*
             * システム用
             */
            // 管理画面「商品詳細」: 画面描画時
            '@admin/Product/product.twig' => 'onDisplayForAdminProductEdit',
            // 管理画面「商品詳細」: 画面読み込み時
            EccubeEvents::ADMIN_PRODUCT_EDIT_INITIALIZE => 'onInitializeForAdminProductEdit',
            // 管理画面「商品CSV出力」: 1行出力時
            EccubeEvents::ADMIN_PRODUCT_CSV_EXPORT => 'onExportForAdminProduct',
            // フロント画面「商品一覧」: 検索条件への介入
            EccubeEvents::FRONT_PRODUCT_INDEX_SEARCH => 'onSearchForProductList',

            /*
             * ユーザー体験向上用
             */
            // フロント画面「商品詳細」: 初めから「EX_PRODUCT」有効化
            'Product/detail.twig' => 'activateExProduct',
            // フロント画面「商品一覧」: 初めから「EX_PRODUCT_LIST」有効化
            'Product/list.twig' => 'activateExProductList',
            // 管理画面「商品一覧」: 初めから「EX_PRODUCT_LIST」有効化
            '@admin/Product/index.twig' => 'activateExProductList',
        ];
    }

    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    /**
     * @var ColumnValueRepository
     */
    private $columnValueRepository;

    public function __construct(EccubeConfig $eccubeConfig, ColumnValueRepository $columnValueRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->columnValueRepository = $columnValueRepository;
    }

    /**
     * 初めから「EX_PRODUCT」有効化( Product が渡されている画面限定 )
     * @param TemplateEvent $event
     */
    public function activateExProduct(TemplateEvent $event)
    {
        if ($this->eccubeConfig['sheeb_expand_product_column.product.detail.seo.activate']) {
            $event->addAsset('@SheebExpandProductColumn/block/seo.twig');
        }
        $event->setParameter('EX_PRODUCT', $this->columnValueRepository->getExProduct(
            $event->getParameter('Product')
        ));
    }

    /**
     * 初めから「EX_PRODUCT_LIST」有効化( Product の pagination が渡されている画面限定 )
     * @param TemplateEvent $event
     */
    public function activateExProductList(TemplateEvent $event)
    {
        $event->setParameter('EX_PRODUCT_LIST', $this->columnValueRepository->getExProductList(
            $event->getParameter('pagination')
        ));
    }


    /**
     * 管理画面「商品詳細」: 画面読み込み時
     *
     * 商品新規追加時に保存した Session 情報があれば保存する
     * @param EventArgs $event@
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onInitializeForAdminProductEdit(EventArgs $event)
    {
        $this->columnValueRepository->saveBySession($event->getArgument('Product'));
    }

    /**
     * 管理画面「商品詳細」: 画面描画時
     *
     * アセットを渡す
     * @param TemplateEvent $event
     */
    public function onDisplayForAdminProductEdit(TemplateEvent $event)
    {
        $event->addAsset('@SheebExpandProductColumn/admin/css/product.twig');
        $event->addSnippet('@SheebExpandProductColumn/admin/js/product.twig');
    }

    /**
     * 管理画面「商品CSV出力」: 1行出力時
     *
     * @param EventArgs $event
     */
    public function onExportForAdminProduct(EventArgs $event)
    {
        /**
         * @var ExportCsvRow $ExportCsvRow
         * @var Csv $Csv
         * @var ProductClass $ProductClass
         */
        $ExportCsvRow = $event->getArgument('ExportCsvRow');
        $Csv = $event->getArgument('Csv');
        $ProductClass = $event->getArgument('ProductClass');
        $Product = $ProductClass->getProduct();

        if ($Csv->getEntityName() !== ColumnValue::class) {
            return;
        }

        // このフィールドにカラムIDが保存してある
        $columnId = $Csv->getReferenceFieldName();

        // Product ごとに ColumnValue をまとめて取得
        if (!isset($this->cacheColumnValues[$Product->getid()])) {
            // メモリ削減のため以前の Product のキャッシュは消す
            $this->cacheColumnValues = [];

            /**
             * @var ColumnValue[] $cache
             */
            $cache = $this->columnValueRepository->findBy([
                'Product' => $Product
            ]);
            $this->cacheColumnValues[$Product->getid()] = array_reduce($cache, function ($reduced, ColumnValue $ColumnValue) {
                $reduced
                    [$ColumnValue->getProductColumn()->getId()]
                    [$ColumnValue->getSequence()]
                    = $ColumnValue;
                return $reduced;
            }, []);
        }

        // 一旦データクリア
        $ExportCsvRow->setData();
        if (isset($this->cacheColumnValues[$Product->getid()][$columnId])) {
            /**
             * @var ColumnValue $oneData
             * @var ColumnValue $ColumnValue
             */
            $values = [];
            $sequences = $this->cacheColumnValues[$Product->getid()][$columnId];
            $oneData = reset($sequences);
            foreach ($sequences as $sequence => $ColumnValue) {
                $values[] = $ColumnValue->getValue();
            }

            // CheckBox はすでにDB内で カンマ区切り保存されているので ハイフンに直す
            if ($oneData->getProductColumn()->getType() === 'checkbox') {
                $values = array_reduce($values, function ($reduced, $checkbox_values) {
                    $reduced[] = str_replace(
                        CheckBoxType::ARRAY_SEPARATOR,
                        CheckBoxType::CSV_GROUPING_SEPARATOR,
                        $checkbox_values
                    );
                    return $reduced;
                });
            }

            if ($oneData->getProductColumn()->getIsMultiple()) {
                $value = implode($this->eccubeConfig['eccube_csv_export_multidata_separator'], $values);
            } else {
                $value = array_shift($values);
            }

            $ExportCsvRow->setData($value);
        }
    }

    /**
     * フロント画面「商品一覧」: 検索条件への介入
     * 検索対象に 追加カラムの値 も含める
     *
     * FIXME: 本来はイベントフックよりもリポジトリのカスタマイズによって実装すべきところ
     * @see http://doc3n.ec-cube.net/customize_repository
     * しかし、2018.08.13 4.0beta ではCustomizerを実装しても動作しない...？
     * サンプルプラグインの「QueryCustomize」を有効化して(コメントアウトを解除して)も動作確認できず。
     * ソースを見ても ServiceProvider から登録していたり、他と実装方針が異なる点をみると
     * このプラグインは古いのかもしれない。
     * というわけで、一旦イベントフックによって実装。
     *
     * @param EventArgs $event
     */
    public function onSearchForProductList(EventArgs $event)
    {
        /**
         * @var array $searchData
         * @var QueryBuilder $qb
         * @var QueryBuilder $mainQueryBuilder
         */
        $searchData = $event->getArgument('searchData');
        $mainQueryBuilder = $event->getArgument('qb');

        if (!isset($searchData['name']) || empty($searchData['name'])) {
            return;
        }

        // isSearchable = 1 , like %Keyword%, をProductごとに取得
        $qb = $this->columnValueRepository->createQueryBuilder('v');
        $qb->where($qb->expr()->like( 'v.value', ':Keyword'))
            ->setParameter('Keyword', "%{$searchData['name']}%");
        $qb->innerJoin('v.ProductColumn', 'c', 'WITH',
			'c.id = v.ProductColumn AND c.isSearchable = 1');
		// 公開中のものだけ
		$qb->innerJoin('v.Product', 'p', 'WITH',
            'p.id = v.Product AND p.Status = 1');
        $ColumnValues = $qb->getQuery()->getResult();

        if (!empty($ColumnValues)) {
            $productIds = array_reduce($ColumnValues, function ($reduced, ColumnValue $ColumnValue) {
                $reduced[] = $ColumnValue->getProduct()->getId();
                return $reduced;
            }, []);
            $mainQueryBuilder->orWhere($mainQueryBuilder->expr()->in('p.id', $productIds));
        }
    }
}
