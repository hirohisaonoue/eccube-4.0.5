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

namespace Plugin\SheebExpandProductColumn\Form\Extension;

use Eccube\Entity\Product;
use Eccube\Form\Type\Admin\ProductType;
use Plugin\SheebExpandProductColumn\Entity\ColumnValue;
use Plugin\SheebExpandProductColumn\Entity\ProductColumn;
use Plugin\SheebExpandProductColumn\Form\Type\Admin\ColumnValueType;
use Plugin\SheebExpandProductColumn\Repository\ColumnValueRepository;
use Plugin\SheebExpandProductColumn\Repository\ProductColumnRepository;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ProductTypeExtension extends AbstractTypeExtension
{
    /**
     * @var ColumnValueRepository
     */
    private $columnValueRepository;

    /**
     * @var ProductColumnRepository
     */
    private $productColumnRepository;
    
    /**
     * @param ColumnValueRepository $columnValueRepository
     * @param ProductColumnRepository $productColumnRepository
     */
    function __construct(ColumnValueRepository $columnValueRepository, ProductColumnRepository $productColumnRepository)
    {
        $this->columnValueRepository = $columnValueRepository;
        $this->productColumnRepository = $productColumnRepository;
    }

    /**
     * {@inheritdoc}
     */
    function getExtendedType()
    {
        return ProductType::class;
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $ProductColumns = $this->productColumnRepository->findAll();
            if (empty($ProductColumns)) {
                return;
            }

            /**
             * @var $Product Product
             */
            $Product = $event->getData();
            $ColumnValues = [];
            if (is_numeric($Product->getId())) {
                $ColumnValues = $this->columnValueRepository->findBy([
                    'Product' => $Product
                ]);
            }

            $form = $event->getForm();
            self::addExColumns($form, $Product, $ProductColumns, $ColumnValues);
        });
    }

    /**
     * @param FormInterface $form
     * @param Product $Product
     * @param ProductColumn[] $ProductColumns
     * @param ColumnValue[]|array $ColumnValues
     */
    static function addExColumns(FormInterface $form, Product $Product, $ProductColumns, $ColumnValues = [], $newColumnValue = null)
    {
        /**
         * @var ColumnValue $ColumnValue
         */
        $columnValueMap = [];
        foreach ($ColumnValues as $ColumnValue) {
            $columnValueMap[$ColumnValue->getProductColumn()->getId()][$ColumnValue->getSequence()] = $ColumnValue;
        }
        
        // 拡張項目のroot
        $code = 'column_values';
        $form->add($code, CollectionType::class, [
            'entry_type' => ColumnValueType::class,
            'label' => 'sheeb_expand_product_column.admin.product.expand_columns',
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'eccube_form_options' => [
                'auto_render' => true,
                'form_theme' => '@SheebExpandProductColumn/admin/form_theme.twig'
            ]
        ]);
        $root = $form->get($code);

        $data = [];
        foreach ($ProductColumns as $ProductColumn) {
            $productColumnId = $ProductColumn->getId();

            // DBに保存されていないColumnValueなら valueのない値を作成
            if (!isset($columnValueMap[$productColumnId])) {
                $ColumnValue = new ColumnValue();
                $ColumnValue->setProduct($Product);
                $ColumnValue->setProductColumn($ProductColumn);
                $ColumnValue->setSequence(0);
                $data[] = $ColumnValue;
                continue;
            }
            
            // DBに保存されているColumnValueならそれをそのまま追加
            foreach ($columnValueMap[$productColumnId] as $ColumnValue) {
                $data[] = $ColumnValue;
            }
        }

        if (!is_null($newColumnValue)) {
            $data[] = $newColumnValue;
        }
        
        $root->setData($data);
    }
}
