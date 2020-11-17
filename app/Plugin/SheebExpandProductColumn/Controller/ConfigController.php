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

namespace Plugin\SheebExpandProductColumn\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Eccube\Common\EccubeConfig;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Repository\CsvRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Plugin\SheebExpandProductColumn\Entity\ColumnValue;
use Plugin\SheebExpandProductColumn\Entity\ProductColumn;
use Plugin\SheebExpandProductColumn\Form\Type\Admin\ProductColumnConfigType;
use Plugin\SheebExpandProductColumn\Repository\ColumnValueRepository;
use Plugin\SheebExpandProductColumn\Repository\ProductColumnRepository;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ConfigController
 */
class ConfigController extends AbstractController
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var ColumnValueRepository
     */
    private $columnValueRepository;
    
    /**
     * @var ProductColumnRepository 
     */
    private $productColumnRepository;

    /**
     * @var CsvRepository
     */
    private $csvRepository;

    /**
     * @param ProductColumnRepository $productColumnRepository
     * @param ColumnValueRepository $columnValueRepository
     * @param CsvRepository $csvRepository
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        ProductColumnRepository $productColumnRepository,
        ColumnValueRepository $columnValueRepository,
        CsvRepository $csvRepository,
        EccubeConfig $eccubeConfig)
    {
        $this->productColumnRepository = $productColumnRepository;
        $this->columnValueRepository = $columnValueRepository;
        $this->csvRepository = $csvRepository;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * @Route("/%eccube_admin_route%/sheeb_expand_product_column/config", name="sheeb_expand_product_column_admin_config")
     * @Template("@SheebExpandProductColumn/admin/config.twig")
     *
     * @param Request $request
     * @param ProductColumnRepository $productColumnRepository
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Request $request, ProductColumnRepository $productColumnRepository)
    {
        $builder = $this->formFactory->createBuilder(ProductColumnConfigType::class);
        $form = $builder->getForm();
        
        // type, is_multiple, is_searchable を人が読む文字に戻す 
        $types = $this->eccubeConfig['sheeb_expand_product_column.choices.column_types'];
        $reverse_types = [];
        foreach ($types as $key => $type) {
            $reverse_types[$type] = trans($key);
        }
        $switches = $this->eccubeConfig['sheeb_expand_product_column.choices.switch'];
        $reverse_switches = [];
        foreach ($switches as $key => $switch) {
            $reverse_switches[$switch] = trans($key);
        }
        
        return [
            'form' => $form->createView(),
            'ProductColumns' => array_reduce($productColumnRepository->findAll(), function ($reduced, ProductColumn $ProductColumn) use ($reverse_types, $reverse_switches){
                $ProductColumn->setType($reverse_types[$ProductColumn->getType()]);
                $ProductColumn->setIsMultiple($reverse_switches[$ProductColumn->getIsMultiple()]);
                $ProductColumn->setIsSearchable($reverse_switches[$ProductColumn->getIsSearchable()]);
                $reduced[] = $ProductColumn;
                return $reduced;
            }, [])
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/sheeb_expand_product_column/config/new", name="sheeb_expand_product_column_admin_config_new")
     * @Template("@SheebExpandProductColumn/admin/config_edit.twig")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(Request $request)
    {
        $ProductColumn = new ProductColumn();
        $builder = $this->formFactory
            ->createBuilder(ProductColumnConfigType::class, $ProductColumn);
        
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ProductColumn = $form->getData();

            // ProductColumn の ID を確定させるために flush
            try {
                $this->entityManager->persist($ProductColumn);
                $this->entityManager->flush($ProductColumn);
            } catch (UniqueConstraintViolationException $e) {
                // codeの重複エラー処理
                $message = trans('sheeb_expand_product_column.admin.save.failure.duplicated_code');
                $this->addError($message, 'admin');
                return $this->redirectToRoute('sheeb_expand_product_column_admin_config_new');
            }

            $sortNo = $this->csvRepository->createQueryBuilder('c')
                ->select('COALESCE(MAX(c.sort_no), 0)')
                ->getQuery()
                ->getSingleScalarResult();
            
            // CSV情報を追加する
            $CsvType = $this->entityManager->getRepository(CsvType::class)->find(CsvType::CSV_TYPE_PRODUCT);
            
            $Csv = new Csv();
            $Csv->setCsvType($CsvType)
                ->setEntityName(ColumnValue::class)
                ->setFieldName('value')
                ->setReferenceFieldName($ProductColumn->getId())
                ->setDispName($ProductColumn->getName())
                ->setSortNo($sortNo);
            $this->entityManager->persist($Csv);

            // CSV情報を保存する
            $ProductColumn->setCsv($Csv);

            // commit
            $this->entityManager->flush([$ProductColumn, $Csv]);

            log_info('ProductColumn config', ['status' => 'Success']);
            $this->addSuccess('sheeb_expand_product_column.admin.save.complete', 'admin');

            return $this->redirectToRoute('sheeb_expand_product_column_admin_config');
        }

        return [
            'form' => $form->createView(),
            'ProductColumn' => $ProductColumn,
            'settingAreaClass' => 'hidden',
            'readOnly' => false
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/sheeb_expand_product_column/config/{id}/edit", requirements={"id" = "\d+"}, name="sheeb_expand_product_column_admin_config_edit")
     * @Template("@SheebExpandProductColumn/admin/config_edit.twig")
     * 
     * @param Request $request
     * @param ProductColumn $ProductColumn
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Request $request, ProductColumn $ProductColumn)
    {
        $builder = $this->formFactory
            ->createBuilder(ProductColumnConfigType::class, $ProductColumn);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * CSVのnameも更新する
             * @var $Csv Csv
             */
            $Csv = $this->entityManager->getRepository(Csv::class)
                ->find($ProductColumn->getCsv()->getId());
            $Csv->setDispName($ProductColumn->getName());

            $this->productColumnRepository->save($ProductColumn);

            // commit
            try {
                $this->entityManager->flush([$ProductColumn, $Csv]);
            } catch (UniqueConstraintViolationException $e) {
                // codeの重複エラー処理
                $message = trans('sheeb_expand_product_column.admin.save.failure.duplicated_code');
                $this->addError($message, 'admin');
                return $this->redirectToRoute('sheeb_expand_product_column_admin_config_edit', ['id' => $ProductColumn->getId()]);
            }

            $this->addSuccess('sheeb_expand_product_column.admin.save.complete', 'admin');
            return $this->redirectToRoute('sheeb_expand_product_column_admin_config');
        }

        switch($ProductColumn->getType()) {
            case 'selectbox':
            case 'radiobutton':
            case 'checkbox':
                $showSetting = true;
                break;
            default:
                $showSetting = false;
                break;
        }

        return [
            'form' => $form->createView(),
            'ProductColumn' => $ProductColumn,
            'settingAreaClass' => $showSetting ? '' : 'hidden',
            'readOnly' => true
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/sheeb_expand_product_column/config/{id}/delete", requirements={"id" = "\d+"}, name="sheeb_expand_product_column_admin_config_delete", methods={"DELETE"})
     * 
     * @param Request $request
     * @param ProductColumn $ProductColumn
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, ProductColumn $ProductColumn)
    {
        log_info('項目削除開始', [$ProductColumn->getId()]);

        try {
            // カラムの値削除
            $ColumnValues = $this->columnValueRepository->findBy([
                'ProductColumn' => $ProductColumn
            ]);
            $this->columnValueRepository->remove($ColumnValues);
            
            // カラム削除
            $this->productColumnRepository->delete($ProductColumn);

            // CSV削除
            $Csv = $this->csvRepository->find($ProductColumn->getCsv()->getId());
            if (!empty($Csv)) {
                $this->csvRepository->delete($Csv);
            }
            
            $this->entityManager->flush();
            $this->addSuccess('sheeb_expand_product_column.admin.delete.complete', 'admin');

            log_info('項目削除完了', [$ProductColumn->getId()]);
        } catch (\Exception $e) {
            log_info('項目削除エラー', [$ProductColumn->getId(), $e]);

            $message = '削除失敗';
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('sheeb_expand_product_column_admin_config');
    }
}