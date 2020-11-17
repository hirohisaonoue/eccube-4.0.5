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

namespace Plugin\SheebExpandProductColumn\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Product;
use Eccube\Repository\ProductRepository;
use Plugin\SheebExpandProductColumn\Entity\ColumnValue;
use Plugin\SheebExpandProductColumn\Entity\ProductColumn;
use Plugin\SheebExpandProductColumn\Repository\ColumnValueRepository;
use Plugin\SheebExpandProductColumn\Repository\ProductColumnRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ColumnValueType
 */
class ColumnValueType extends AbstractType
{
    const DELETE_CODE = '%%__dEl__%%';

    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;
    
    /**
     * @var ProductRepository
     */
    private $productRepository;
    
    /**
     * @var ProductColumnRepository
     */
    private $productColumnRepository;

    /**
     * @var ColumnValueRepository
     */
    private $columnValueRepository;

    /**
     * @param Request $request
     * @param EccubeConfig $eccubeConfig
     * @param ProductRepository $productRepository
     * @param ProductColumnRepository $productColumnRepository
     * @param ColumnValueRepository $columnValueRepository
     */
    function __construct(
        EccubeConfig $eccubeConfig,
        ProductRepository $productRepository,
        ProductColumnRepository $productColumnRepository,
        ColumnValueRepository $columnValueRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->productRepository = $productRepository;
        $this->productColumnRepository = $productColumnRepository;
        $this->columnValueRepository = $columnValueRepository;
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
         * [POST_SET_DATA]
         * DBに保存してある追加項目をセット
         */
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            /**
             * @var $ColumnValue ColumnValue
             */
            $ColumnValue = $event->getData();
            if (empty($ColumnValue)) {
                return;
            }

            // 前後データの存在を確認するために取得
            $ColumnValues = $this->columnValueRepository->findBy(
                [
                    'Product' => $ColumnValue->getProduct(),
                    'ProductColumn' => $ColumnValue->getProductColumn()
                ],
                [
                    'sequence' => 'ASC'
                ]
            );

            self::addColumnValueFields(
                $event->getForm(), $ColumnValue->getProductColumn(),
                $ColumnValue, $ColumnValues
            );
        });

        /*
         * [PRE_SUBMIT]
         * フロントで動的に追加した項目は form に情報がないので追加する
         */
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            /**
             * @var $ColumnValue ColumnValue
             */
            $data = $event->getData();
            $form = $event->getForm();
            $ColumnValue = $form->getData();

            if (empty($ColumnValue)) {
                $ColumnValue = new ColumnValue();
                $ColumnValue->setProductColumn($this->productColumnRepository->find($data['ProductColumn']));
                $ColumnValue->setSequence($data['sequence']);

                if (!empty($data['Product'])) {
                    $ColumnValue->setProduct($this->productRepository->find($data['Product']));
                }
            }

            if (empty($ColumnValue->getProduct())) {
                $Product = new Product();
                $Product->setId(0);
                $ColumnValue->setProduct($Product);
            }
            
            $ProductColumn = $ColumnValue->getProductColumn();
            
            $canSetData = false;
            
            // 空データは削除対象とする
            if (empty($data['value'])) {
                $data['value'] = self::DELETE_CODE;
                $ColumnValue->setValue(self::DELETE_CODE);
            }

            /*
             * フロントによる追加項目は addColumn 処理をいれて form に認識させる
             */
            if (is_null($form->getNormData())) {
                // field 情報追加
                self::addColumnValueFields($form, $ProductColumn);
                // value をセット
                $ColumnValue->setValue($data['value']);
                $canSetData = true;
            }

            /*
             * 画像の場合は
             * - temp から save ディレクトリに移動する
             * - 値を File型 に変換
             */
            if ($ProductColumn->getType() === 'images') {
                $path = $this->eccubeConfig['eccube_temp_image_dir'] . '/' . $data['value'];

                $fileName = self::DELETE_CODE;
                clearstatcache();
                if (is_file($path)) {
                    $File = new File($path);
                    $File = $File->move($this->eccubeConfig['eccube_save_image_dir'], $data['value']);
                    $fileName = $File->getFilename();
                }
                
                // value を File で上書き
                $ColumnValue->setValue($fileName);
                $canSetData = true;
            }

            if ($canSetData) {
                $form->setData($ColumnValue);
            }
        });
        
        /*
         * [POST_SUBMIT]
         * 追加項目の値をDBに登録
         */
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /**
             * @var $ColumnValue ColumnValue
             */
            $ColumnValue = $event->getData();
            if (empty($ColumnValue->getProduct())) {
                return;
            }

            // 画像 は独自保存なので 存在チェック & データセット
            if ($ColumnValue->getProductColumn()->getType() === 'images') {
                $fileName = $ColumnValue->getValue();
                $ExistColumnValue = $this->columnValueRepository->findOneBy([
                    'Product' => $ColumnValue->getProduct(),
                    'ProductColumn' => $ColumnValue->getProductColumn(),
                    'sequence' => $ColumnValue->getSequence(),
                ]);
                $ColumnValue = empty($ExistColumnValue) ? $ColumnValue : $ExistColumnValue;
                $ColumnValue->setValue($fileName);
            }
            
            $doDelete = false;
            if (!empty($ColumnValue->getProduct()->getId()) && 
                ($ColumnValue->getValue() === self::DELETE_CODE || empty($ColumnValue->getValue()))) {
                $doDelete = true;
            }
            
            if ($doDelete) {
                $this->columnValueRepository->delete($ColumnValue);
            } else {
                $this->columnValueRepository->save($ColumnValue);
            }
        });
    }

    /**
     * @param FormInterface $form
     * @param ProductColumn $ProductColumn
     * @param ColumnValue|null $ColumnValue
     * @param array $ColumnValues
     */
    static private function addColumnValueFields(FormInterface $form, ProductColumn $ProductColumn, ColumnValue $ColumnValue = null, $ColumnValues = [])
    {
        // sequenceをkeyとする
        $columnValueMap = array_reduce($ColumnValues, function ($reduced, ColumnValue $record) {
            $reduced[$record->getSequence()] = $record;
            return $reduced;
        }, []);
        

        $form
            ->add('Product', HiddenType::class, [
                'property_path' => 'Product.id',
            ])
            ->add('ProductColumn', HiddenType::class, [
                'property_path' => 'ProductColumn.id',
            ])
            ->add('sequence', HiddenType::class)
            ->add('value', self::getType($ProductColumn->getType()),
                self::getTypeOptions($ProductColumn, $ColumnValue, $columnValueMap)
            );
    }


    /**
     * @param $type
     * @return string
     */
    static private function getType($type)
    {
        switch ($type) {
            case 'text':
                $result = TextType::class;
                break;
            case 'textarea':
                $result = TextareaType::class;
                break;
            case 'selectbox':
            case 'radiobutton':
                $result = ChoiceType::class;
                break;
            case 'checkbox':
                $result = CheckBoxType::class;
                break;
            case 'images':
                $result = CustomFileType::class;
                break;
            default:
                $result = TextType::class;
                break;
        }

        return $result;
    }

    /**
     * @param ProductColumn $ProductColumn
     * @param ColumnValue|null $ColumnValue
     * @param ColumnValue[] $columnValueMap
     * @return array
     */
    static private function getTypeOptions(ProductColumn $ProductColumn, ColumnValue $ColumnValue = null, $columnValueMap = [])
    {
        // 描画に必要な情報をセットしておく
        $ex_options = [
            'type' => $ProductColumn->getType(),
            'multiple' => $ProductColumn->getIsMultiple(),
            'searchable' => $ProductColumn->getIsSearchable(),
            'product_id' => 0,
            'column_id' => 0,
            'sequence' => 0,
            'next' => 1,
            'prev' => -1,
        ];
        
        // ColumnValue 情報
        if (!is_null($ColumnValue)) {
            $ex_options['product_id'] = empty($ColumnValue->getProduct()->getId()) ? 0 : $ColumnValue->getProduct()->getId();
            $ex_options['column_id'] = $ColumnValue->getProductColumn()->getId();
            $ex_options['sequence'] = $ColumnValue->getSequence();
            $ex_options['next'] = $ColumnValue->getSequence() + 1;
            $ex_options['prev'] = $ex_options['sequence'] > 0 ? $ex_options['sequence'] - 1 : null;

            $ex_options['next_exist'] = isset($columnValueMap[$ex_options['next']]);
            $ex_options['prev_exist'] = isset($columnValueMap[$ex_options['prev']]);
        }
        
        $options = [
            'label' => $ProductColumn->getName(),
            'eccube_form_options' => $ex_options
        ];
        
        switch ($ProductColumn->getType()) {
            case 'text':
            case 'textarea':
                $result = $options;
                break;
            case 'selectbox':
                $setting = $ProductColumn->getSetting();
                $options['choices'] = self::settingToArray($setting);
                $result = $options;

                break;
            case 'radiobutton':
                $setting = $ProductColumn->getSetting();
                $options['expanded'] = true;
                $options['choices'] = self::settingToArray($setting);
                $result = $options;

                break;
            case 'checkbox':
                $setting = $ProductColumn->getSetting();
                $options['multiple'] = true;
                $options['expanded'] = true;
                $options['choices'] = self::settingToArray($setting);
                $result = $options;

                break;
            case 'images':
                $result = $options;
                break;
            default:
                $result = $options;
                break;
        }

        return $result;
    }

    static function settingToArray($setting)
    {
        $result = array();
        $setting = preg_replace("/\r\n|\r|\n/", "\n", $setting); // 改行コード統一
        $array = explode("\n", $setting); // とりあえず行に分割
        $array = preg_replace("/( |　)/", "", $array ); // 空白を全て取り除く
        $array = array_filter($array, 'strlen'); // 文字数が0の行を取り除く
        $array = array_values($array); // これはキーを連番に振りなおしてるだけ

        foreach ($array as $value) {
            $wk_arr = explode(":", $value);
            if (count($wk_arr) === 2) {
                $result[$wk_arr[0]] = $wk_arr[1];
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ColumnValue::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    function getBlockPrefix()
    {
        return 'sheeb_expand_product_column_value';
    }
}
