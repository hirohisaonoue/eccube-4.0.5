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

use Eccube\Common\EccubeConfig;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Product;
use Eccube\Form\Type\Admin\ProductType;
use Eccube\Repository\ProductRepository;
use Plugin\SheebExpandProductColumn\Entity\ColumnValue;
use Plugin\SheebExpandProductColumn\Entity\ProductColumn;
use Plugin\SheebExpandProductColumn\Form\Extension\ProductTypeExtension;
use Plugin\SheebExpandProductColumn\Form\Type\Admin\ColumnValueType;
use Plugin\SheebExpandProductColumn\Repository\ColumnValueRepository;
use Plugin\SheebExpandProductColumn\Repository\ProductColumnRepository;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * Class ApiController
 */
class ApiController extends AbstractController
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
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductColumnRepository $productColumnRepository
     * @param ProductRepository $productRepository
     * @param ColumnValueRepository $columnValueRepository
     */
    public function __construct(ProductColumnRepository $productColumnRepository, ProductRepository $productRepository, ColumnValueRepository $columnValueRepository)
    {
        $this->columnValueRepository = $columnValueRepository;
        $this->productColumnRepository = $productColumnRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/sheeb_expand_product_column/column_value/{product_id}/{column_id}/{sequence}/add/{number}", requirements={"product_id" = "\d+","column_id" = "\d+", "sequence" = "\d+", "number" = "\d+"}, methods={"GET"}, name="")
     * @Template("@SheebExpandProductColumn/admin/column_value.twig")
     *
     * @param int $product_id
     * @param int $column_id
     * @param int $sequence
     * @param int $number
     * @return array
     */
    public function createElement($product_id, $column_id, $sequence, $number)
    {
        /**
         * 新しく作る ColumnValue
         * @var Product $Product
         * @var ProductColumn $ProductColumn
         */
        $Product = $this->productRepository->find($product_id);
        if (empty($Product)) {
            $Product = new Product();
            $Product->setId(0);
        }
        $ProductColumn = $this->productColumnRepository->find($column_id);
        $ColumnValue = new ColumnValue();
        $ColumnValue->setProduct($Product);
        $ColumnValue->setProductColumn($ProductColumn);
        $ColumnValue->setSequence($sequence);

        // 画面描画時と同じ方法で form を作成する
        // (ただし$ColumnValuesは DBからデータを取得するとフロントで増やした項目分ずれてしまうのでダミーを作成)
        $ColumnValues = [];
        for ($i = 1; $i < $number - 1; $i++) {
            $dummy = new ColumnValue();
            $dummy->setProduct($Product);
            $dummy->setProductColumn($ProductColumn);
            $dummy->setSequence(-$i);
            $ColumnValues[] = clone $dummy;
        }

        $builder = $this->formFactory->createBuilder(ProductType::class);
        $builder->setData($Product);
        $form = $builder->getForm();

        $ProductColumns = $this->productColumnRepository->findAll();
        ProductTypeExtension::addExColumns($form, $Product, $ProductColumns, $ColumnValues, $ColumnValue);
        
        // form から今新しく作ったものを取得する
        $children = $form->createView()->children['column_values']->children;
        $newField = null;
        foreach ($children as $child) {
            $childProductId = intval($child->children['Product']->vars['value']);
            $childProductColumnId = intval($child->children['ProductColumn']->vars['value']);
            $childSequence = intval($child->children['sequence']->vars['value']);

            if ($childProductId !== $Product->getId() || $childProductColumnId !== $ProductColumn->getId()) {
                continue;
            }

            if (is_null($newField)) {
                $newField = clone $child;
                continue;
            }
            
            $newFieldSequence = intval($newField->children['sequence']->vars['value']);
            
            if ($childSequence > $newFieldSequence) {
                $newField = clone $child;
            }
        }
        return [
            // 作成した form の最後の要素を渡す
            'form' => $newField,
            'options' => [
                'multiple' => $ProductColumn->getIsMultiple(),
                'searchable' => $ProductColumn->getIsSearchable(),
                'product_id' => $product_id,
                'column_id' => $column_id,
                'sequence' => $sequence,
                'next' => $sequence + 1,
                'prev' => $sequence - 1,
                'next_exist' => false,
                'prev_exist' => true,
            ]
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/sheeb_expand_product_column/image/add", name="sheeb_expand_product_column_image_add", methods={"POST"})
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addImage(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('リクエストが不正です');
        }

        $images = $request->files->get('files');

        $files = [];
        if (count($images) > 0) {
            foreach ($images as $image) {

                //ファイルフォーマット検証
                $mimeType = $image->getMimeType();
                if (0 !== strpos($mimeType, 'image')) {
                    throw new UnsupportedMediaTypeHttpException('ファイル形式が不正です');
                }

                $extension = $image->getClientOriginalExtension();
                $filename = date('mdHis').uniqid('_').'.'.$extension;
                $image->move($this->eccubeConfig['eccube_temp_image_dir'], $filename);
                $files[] = $filename;
            }
        }

        return $this->json(['files' => $files], 200);
    }
}