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

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Common\Constant;
use Eccube\Controller\Admin\AbstractCsvImportController;
use Eccube\Controller\Admin\Product\CsvImportController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Category;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\ProductTag;
use Eccube\Form\Type\Admin\CsvImportType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\DeliveryDurationRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TagRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\CsvImportService;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Plugin\SheebExpandProductColumn\Entity\ColumnValue;
use Plugin\SheebExpandProductColumn\Entity\ProductColumn;
use Plugin\SheebExpandProductColumn\Form\Type\Admin\CheckBoxType;
use Plugin\SheebExpandProductColumn\Form\Type\Admin\ColumnValueType;
use Plugin\SheebExpandProductColumn\Repository\ColumnValueRepository;
use Plugin\SheebExpandProductColumn\Repository\ProductColumnRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ApiController
 */
class ProductCsvImportController extends CsvImportController
{
    /**
     * @var ProductColumn[]
     */
    private $ProductColumns;
    
    /**
     * @var array
     */
    private $cacheProductHeaders;
    
    /**
     * @var array
     */
    private $cacheExProductHeaders;
    
    /**
     * @var ColumnValueRepository
     */
    private $columnValueRepository;
    
    /**
     * @var ProductColumnRepository
     */
    private $productColumnRepository;

    /**
     * @var DeliveryDurationRepository
     */
    protected $deliveryDurationRepository;

    /**
     * @var SaleTypeRepository
     */
    protected $saleTypeRepository;

    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;

    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var TaxRuleRepository
     */
    private $taxRuleRepository;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    private $errors = [];

    /**
     * @param ProductColumnRepository $productColumnRepository
     * @param ColumnValueRepository $columnValueRepository
     * @param DeliveryDurationRepository $deliveryDurationRepository
     * @param SaleTypeRepository $saleTypeRepository
     * @param TagRepository $tagRepository
     * @param CategoryRepository $categoryRepository
     * @param ClassCategoryRepository $classCategoryRepository
     * @param ProductStatusRepository $productStatusRepository
     * @param ProductRepository $productRepository
     * @param TaxRuleRepository $taxRuleRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param ValidatorInterface $validator
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __construct(
        ProductColumnRepository $productColumnRepository,
        ColumnValueRepository $columnValueRepository,
        DeliveryDurationRepository $deliveryDurationRepository,
        SaleTypeRepository $saleTypeRepository,
        TagRepository $tagRepository,
        CategoryRepository $categoryRepository,
        ClassCategoryRepository $classCategoryRepository,
        ProductStatusRepository $productStatusRepository,
        ProductRepository $productRepository,
        TaxRuleRepository $taxRuleRepository,
        BaseInfoRepository $baseInfoRepository,
        ValidatorInterface $validator
    ) {
        $this->deliveryDurationRepository = $deliveryDurationRepository;
        $this->saleTypeRepository = $saleTypeRepository;
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->classCategoryRepository = $classCategoryRepository;
        $this->productStatusRepository = $productStatusRepository;
        $this->productRepository = $productRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->validator = $validator;

        $this->columnValueRepository = $columnValueRepository;
        $this->productColumnRepository = $productColumnRepository;
    }

    /**
     * 商品登録CSVアップロード
     *
     * @Route("/%eccube_admin_route%/product/product_csv_upload", name="admin_product_csv_import")
     * @Template("@admin/Product/csv_product.twig")
     * @throws \Doctrine\ORM\NoResultException
     */
    public function csvProduct(Request $request, CacheUtil $cacheUtil)
    {
        $form = $this->formFactory->createBuilder(CsvImportType::class)->getForm();
        $headers = $this->getProductCsvHeader();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $formFile = $form['import_file']->getData();
                if (!empty($formFile)) {
                    log_info('商品CSV登録開始');
                    $data = $this->getImportData($formFile);
                    if ($data === false) {
                        $this->addErrors(trans('admin.common.csv_invalid_format'));

                        return $this->renderWithError($form, $headers, false);
                    }
                    $getId = function ($item) {
                        return $item['id'];
                    };
                    $requireHeader = array_keys(array_map($getId, array_filter($headers, function ($value) {
                        return $value['required'];
                    })));

                    $columnHeaders = $data->getColumnHeaders();

                    if (count(array_diff($requireHeader, $columnHeaders)) > 0) {
                        $this->addErrors(trans('admin.common.csv_invalid_format'));

                        return $this->renderWithError($form, $headers, false);
                    }

                    $size = count($data);

                    if ($size < 1) {
                        $this->addErrors(trans('admin.common.csv_invalid_no_data'));

                        return $this->renderWithError($form, $headers, false);
                    }

                    $headerSize = count($columnHeaders);
                    $headerByKey = array_flip(array_map($getId, $headers));
                    $deleteImages = [];

                    $this->entityManager->getConfiguration()->setSQLLogger(null);
                    $this->entityManager->getConnection()->beginTransaction();
                    // CSVファイルの登録処理
                    foreach ($data as $row) {
                        $line = $data->key() + 1;
                        if ($headerSize != count($row)) {
                            $message = trans('admin.common.csv_invalid_format_line', ['%line%' => $line]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        }

                        if (!isset($row[$headerByKey['id']]) || StringUtil::isBlank($row[$headerByKey['id']])) {
                            $Product = new Product();
                            $this->entityManager->persist($Product);
                        } else {
                            if (preg_match('/^\d+$/', $row[$headerByKey['id']])) {
                                $Product = $this->productRepository->find($row[$headerByKey['id']]);
                                if (!$Product) {
                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['id']]);
                                    $this->addErrors($message);

                                    return $this->renderWithError($form, $headers);
                                }
                            } else {
                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['id']]);
                                $this->addErrors($message);

                                return $this->renderWithError($form, $headers);
                            }

                            if (isset($row[$headerByKey['product_del_flg']])) {
                                if (StringUtil::isNotBlank($row[$headerByKey['product_del_flg']]) && $row[$headerByKey['product_del_flg']] == (string) Constant::ENABLED) {
                                    // 商品を物理削除
                                    $deleteImages[] = $Product->getProductImage();

                                    try {
                                        $this->productRepository->delete($Product);
                                        $this->entityManager->flush();

                                        continue;
                                    } catch (ForeignKeyConstraintViolationException $e) {
                                        $message = trans('admin.common.csv_invalid_foreign_key', ['%line%' => $line, '%name%' => $Product->getName()]);
                                        $this->addErrors($message);

                                        return $this->renderWithError($form, $headers);
                                    }
                                }
                            }
                        }

                        if (StringUtil::isBlank($row[$headerByKey['status']])) {
                            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['status']]);
                            $this->addErrors($message);
                        } else {
                            if (preg_match('/^\d+$/', $row[$headerByKey['status']])) {
                                $ProductStatus = $this->productStatusRepository->find($row[$headerByKey['status']]);
                                if (!$ProductStatus) {
                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['status']]);
                                    $this->addErrors($message);
                                } else {
                                    $Product->setStatus($ProductStatus);
                                }
                            } else {
                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['status']]);
                                $this->addErrors($message);
                            }
                        }

                        if (StringUtil::isBlank($row[$headerByKey['name']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['name']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Product->setName(StringUtil::trimAll($row[$headerByKey['name']]));
                        }

                        if (isset($row[$headerByKey['note']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['note']])) {
                                $Product->setNote(StringUtil::trimAll($row[$headerByKey['note']]));
                            } else {
                                $Product->setNote(null);
                            }
                        }

                        if (isset($row[$headerByKey['description_list']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['description_list']])) {
                                $Product->setDescriptionList(StringUtil::trimAll($row[$headerByKey['description_list']]));
                            } else {
                                $Product->setDescriptionList(null);
                            }
                        }

                        if (isset($row[$headerByKey['description_detail']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['description_detail']])) {
                                $Product->setDescriptionDetail(StringUtil::trimAll($row[$headerByKey['description_detail']]));
                            } else {
                                $Product->setDescriptionDetail(null);
                            }
                        }

                        if (isset($row[$headerByKey['search_word']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['search_word']])) {
                                $Product->setSearchWord(StringUtil::trimAll($row[$headerByKey['search_word']]));
                            } else {
                                $Product->setSearchWord(null);
                            }
                        }

                        if (isset($row[$headerByKey['free_area']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['free_area']])) {
                                $Product->setFreeArea(StringUtil::trimAll($row[$headerByKey['free_area']]));
                            } else {
                                $Product->setFreeArea(null);
                            }
                        }

                        // 商品画像登録
                        $this->createProductImage($row, $Product, $data, $headerByKey);

                        $this->entityManager->flush();

                        // 商品カテゴリ登録
                        $this->createProductCategory($row, $Product, $data, $headerByKey);

                        //タグ登録
                        $this->createProductTag($row, $Product, $data, $headerByKey);

                        // 商品規格が存在しなければ新規登録
                        /** @var ProductClass[] $ProductClasses */
                        $ProductClasses = $Product->getProductClasses();
                        if ($ProductClasses->count() < 1) {
                            // 規格分類1(ID)がセットされていると規格なし商品、規格あり商品を作成
                            $ProductClassOrg = $this->createProductClass($row, $Product, $data, $headerByKey);
                            if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                                if (isset($row[$headerByKey['delivery_fee']]) && StringUtil::isNotBlank($row[$headerByKey['delivery_fee']])) {
                                    $deliveryFee = str_replace(',', '', $row[$headerByKey['delivery_fee']]);
                                    $errors = $this->validator->validate($deliveryFee, new GreaterThanOrEqual(['value' => 0]));
                                    if ($errors->count() === 0) {
                                        $ProductClassOrg->setDeliveryFee($deliveryFee);
                                    } else {
                                        $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['delivery_fee']]);
                                        $this->addErrors($message);
                                    }
                                }
                            }

                            // 商品別税率機能が有効の場合に税率を更新
                            if ($this->BaseInfo->isOptionProductTaxRule()) {
                                if (isset($row[$headerByKey['tax_rate']]) && StringUtil::isNotBlank($row[$headerByKey['tax_rate']])) {
                                    $taxRate = $row[$headerByKey['tax_rate']];
                                    $errors = $this->validator->validate($taxRate, new GreaterThanOrEqual(['value' => 0]));
                                    if ($errors->count() === 0) {
                                        if ($ProductClassOrg->getTaxRule()) {
                                            // 商品別税率の設定があれば税率を更新
                                            $ProductClassOrg->getTaxRule()->setTaxRate($taxRate);
                                        } else {
                                            // 商品別税率の設定がなければ新規作成
                                            $TaxRule = $this->taxRuleRepository->newTaxRule();
                                            $TaxRule->setTaxRate($taxRate);
                                            $TaxRule->setApplyDate(new \DateTime());
                                            $TaxRule->setProduct($Product);
                                            $TaxRule->setProductClass($ProductClassOrg);
                                            $ProductClassOrg->setTaxRule($TaxRule);
                                        }
                                    } else {
                                        $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['tax_rate']]);
                                        $this->addErrors($message);
                                    }
                                } else {
                                    // 税率の入力がなければ税率の設定を削除
                                    if ($ProductClassOrg->getTaxRule()) {
                                        $this->taxRuleRepository->delete($ProductClassOrg->getTaxRule());
                                        $ProductClassOrg->setTaxRule(null);
                                    }
                                }
                            }

                            if (isset($row[$headerByKey['class_category1']]) && StringUtil::isNotBlank($row[$headerByKey['class_category1']])) {
                                if (isset($row[$headerByKey['class_category2']]) && $row[$headerByKey['class_category1']] == $row[$headerByKey['class_category2']]) {
                                    $message = trans('admin.common.csv_invalid_not_same', [
                                        '%line%' => $line,
                                        '%name1%' => $headerByKey['class_category1'],
                                        '%name2%' => $headerByKey['class_category2'],
                                    ]);
                                    $this->addErrors($message);
                                } else {
                                    // 商品規格あり
                                    // 規格分類あり商品を作成
                                    $ProductClass = clone $ProductClassOrg;
                                    $ProductStock = clone $ProductClassOrg->getProductStock();

                                    // 規格分類1、規格分類2がnullであるデータを非表示
                                    $ProductClassOrg->setVisible(false);

                                    // 規格分類1、2をそれぞれセットし作成
                                    $ClassCategory1 = null;
                                    if (preg_match('/^\d+$/', $row[$headerByKey['class_category1']])) {
                                        $ClassCategory1 = $this->classCategoryRepository->find($row[$headerByKey['class_category1']]);
                                        if (!$ClassCategory1) {
                                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                                            $this->addErrors($message);
                                        } else {
                                            $ProductClass->setClassCategory1($ClassCategory1);
                                        }
                                    } else {
                                        $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                                        $this->addErrors($message);
                                    }

                                    if (isset($row[$headerByKey['class_category2']]) && StringUtil::isNotBlank($row[$headerByKey['class_category2']])) {
                                        if (preg_match('/^\d+$/', $row[$headerByKey['class_category2']])) {
                                            $ClassCategory2 = $this->classCategoryRepository->find($row[$headerByKey['class_category2']]);
                                            if (!$ClassCategory2) {
                                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                                $this->addErrors($message);
                                            } else {
                                                if ($ClassCategory1 &&
                                                    ($ClassCategory1->getClassName()->getId() == $ClassCategory2->getClassName()->getId())
                                                ) {
                                                    $message = trans('admin.common.csv_invalid_not_same', ['%line%' => $line, '%name1%' => $headerByKey['class_category1'], '%name2%' => $headerByKey['class_category2']]);
                                                    $this->addErrors($message);
                                                } else {
                                                    $ProductClass->setClassCategory2($ClassCategory2);
                                                }
                                            }
                                        } else {
                                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                            $this->addErrors($message);
                                        }
                                    }
                                    $ProductClass->setProductStock($ProductStock);
                                    $ProductStock->setProductClass($ProductClass);

                                    $this->entityManager->persist($ProductClass);
                                    $this->entityManager->persist($ProductStock);
                                }
                            } else {
                                if (isset($row[$headerByKey['class_category2']]) && StringUtil::isNotBlank($row[$headerByKey['class_category2']])) {
                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                    $this->addErrors($message);
                                }
                            }
                        } else {
                            // 商品規格の更新
                            $flag = false;
                            $classCategoryId1 = StringUtil::isBlank($row[$headerByKey['class_category1']]) ? null : $row[$headerByKey['class_category1']];
                            $classCategoryId2 = StringUtil::isBlank($row[$headerByKey['class_category2']]) ? null : $row[$headerByKey['class_category2']];

                            foreach ($ProductClasses as $pc) {
                                $classCategory1 = is_null($pc->getClassCategory1()) ? null : $pc->getClassCategory1()->getId();
                                $classCategory2 = is_null($pc->getClassCategory2()) ? null : $pc->getClassCategory2()->getId();

                                // 登録されている商品規格を更新
                                if ($classCategory1 == $classCategoryId1 &&
                                    $classCategory2 == $classCategoryId2
                                ) {
                                    $this->updateProductClass($row, $Product, $pc, $data, $headerByKey);

                                    if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                                        if (isset($row[$headerByKey['delivery_fee']]) && StringUtil::isNotBlank($row[$headerByKey['delivery_fee']])) {
                                            $deliveryFee = str_replace(',', '', $row[$headerByKey['delivery_fee']]);
                                            $errors = $this->validator->validate($deliveryFee, new GreaterThanOrEqual(['value' => 0]));
                                            if ($errors->count() === 0) {
                                                $pc->setDeliveryFee($deliveryFee);
                                            } else {
                                                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['delivery_fee']]);
                                                $this->addErrors($message);
                                            }
                                        }
                                    }

                                    // 商品別税率機能が有効の場合に税率を更新
                                    if ($this->BaseInfo->isOptionProductTaxRule()) {
                                        if (isset($row[$headerByKey['tax_rate']]) && StringUtil::isNotBlank($row[$headerByKey['tax_rate']])) {
                                            $taxRate = $row[$headerByKey['tax_rate']];
                                            $errors = $this->validator->validate($taxRate, new GreaterThanOrEqual(['value' => 0]));
                                            if ($errors->count() === 0) {
                                                if ($pc->getTaxRule()) {
                                                    // 商品別税率の設定があれば税率を更新
                                                    $pc->getTaxRule()->setTaxRate($taxRate);
                                                } else {
                                                    // 商品別税率の設定がなければ新規作成
                                                    $TaxRule = $this->taxRuleRepository->newTaxRule();
                                                    $TaxRule->setTaxRate($taxRate);
                                                    $TaxRule->setApplyDate(new \DateTime());
                                                    $TaxRule->setProduct($Product);
                                                    $TaxRule->setProductClass($pc);
                                                    $pc->setTaxRule($TaxRule);
                                                }
                                            } else {
                                                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['tax_rate']]);
                                                $this->addErrors($message);
                                            }
                                        } else {
                                            // 税率の入力がなければ税率の設定を削除
                                            if ($pc->getTaxRule()) {
                                                $this->taxRuleRepository->delete($pc->getTaxRule());
                                                $pc->setTaxRule(null);
                                            }
                                        }
                                    }

                                    $flag = true;
                                    break;
                                }
                            }

                            // 商品規格を登録
                            if (!$flag) {
                                $pc = $ProductClasses[0];
                                if ($pc->getClassCategory1() == null &&
                                    $pc->getClassCategory2() == null
                                ) {
                                    // 規格分類1、規格分類2がnullであるデータを非表示
                                    $pc->setVisible(false);
                                }

                                if (isset($row[$headerByKey['class_category1']]) && isset($row[$headerByKey['class_category2']])
                                    && $row[$headerByKey['class_category1']] == $row[$headerByKey['class_category2']]) {
                                    $message = trans('admin.common.csv_invalid_not_same', [
                                        '%line%' => $line,
                                        '%name1%' => $headerByKey['class_category1'],
                                        '%name2%' => $headerByKey['class_category2'],
                                    ]);
                                    $this->addErrors($message);
                                } else {
                                    // 必ず規格分類1がセットされている
                                    // 規格分類1、2をそれぞれセットし作成
                                    $ClassCategory1 = null;
                                    if (preg_match('/^\d+$/', $classCategoryId1)) {
                                        $ClassCategory1 = $this->classCategoryRepository->find($classCategoryId1);
                                        if (!$ClassCategory1) {
                                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                                            $this->addErrors($message);
                                        }
                                    } else {
                                        $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                                        $this->addErrors($message);
                                    }

                                    $ClassCategory2 = null;
                                    if (isset($row[$headerByKey['class_category2']]) && StringUtil::isNotBlank($row[$headerByKey['class_category2']])) {
                                        if ($pc->getClassCategory1() != null && $pc->getClassCategory2() == null) {
                                            $message = trans('admin.common.csv_invalid_can_not', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                            $this->addErrors($message);
                                        } else {
                                            if (preg_match('/^\d+$/', $classCategoryId2)) {
                                                $ClassCategory2 = $this->classCategoryRepository->find($classCategoryId2);
                                                if (!$ClassCategory2) {
                                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                                    $this->addErrors($message);
                                                } else {
                                                    if ($ClassCategory1 &&
                                                        ($ClassCategory1->getClassName()->getId() == $ClassCategory2->getClassName()->getId())
                                                    ) {
                                                        $message = trans('admin.common.csv_invalid_not_same', [
                                                            '%line%' => $line,
                                                            '%name1%' => $headerByKey['class_category1'],
                                                            '%name2%' => $headerByKey['class_category2'],
                                                        ]);
                                                        $this->addErrors($message);
                                                    }
                                                }
                                            } else {
                                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                                $this->addErrors($message);
                                            }
                                        }
                                    } else {
                                        if ($pc->getClassCategory1() != null && $pc->getClassCategory2() != null) {
                                            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                            $this->addErrors($message);
                                        }
                                    }
                                    $ProductClass = $this->createProductClass($row, $Product, $data, $headerByKey, $ClassCategory1, $ClassCategory2);

                                    if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                                        if (isset($row[$headerByKey['delivery_fee']]) && StringUtil::isNotBlank($row[$headerByKey['delivery_fee']])) {
                                            $deliveryFee = str_replace(',', '', $row[$headerByKey['delivery_fee']]);
                                            $errors = $this->validator->validate($deliveryFee, new GreaterThanOrEqual(['value' => 0]));
                                            if ($errors->count() === 0) {
                                                $ProductClass->setDeliveryFee($deliveryFee);
                                            } else {
                                                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['delivery_fee']]);
                                                $this->addErrors($message);
                                            }
                                        }
                                    }

                                    // 商品別税率機能が有効の場合に税率を更新
                                    if ($this->BaseInfo->isOptionProductTaxRule()) {
                                        if (isset($row[$headerByKey['tax_rate']]) && StringUtil::isNotBlank($row[$headerByKey['tax_rate']])) {
                                            $taxRate = $row[$headerByKey['tax_rate']];
                                            $errors = $this->validator->validate($taxRate, new GreaterThanOrEqual(['value' => 0]));
                                            if ($errors->count() === 0) {
                                                $TaxRule = $this->taxRuleRepository->newTaxRule();
                                                $TaxRule->setTaxRate($taxRate);
                                                $TaxRule->setApplyDate(new \DateTime());
                                                $TaxRule->setProduct($Product);
                                                $TaxRule->setProductClass($ProductClass);
                                                $ProductClass->setTaxRule($TaxRule);
                                            } else {
                                                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['tax_rate']]);
                                                $this->addErrors($message);
                                            }
                                        }
                                    }

                                    $Product->addProductClass($ProductClass);
                                }
                            }
                        }
                        if ($this->hasErrors()) {
                            return $this->renderWithError($form, $headers);
                        }
                        $this->entityManager->persist($Product);
                        
                        // 追加 >>>>>>>
                        $this->editColumnValue($Product, $row);
                        // 追加 <<<<<<<
                    }
                    // 追加 >>>>>>>
                    if ($this->hasErrors()) {
                        return $this->renderWithError($form, $headers);
                    }
                    // 追加 <<<<<<<

                    $this->entityManager->flush();
                    $this->entityManager->getConnection()->commit();

                    // 画像ファイルの削除(commit後に削除させる)
                    foreach ($deleteImages as $images) {
                        foreach ($images as $image) {
                            try {
                                $fs = new Filesystem();
                                $fs->remove($this->eccubeConfig['eccube_save_image_dir'].'/'.$image);
                            } catch (\Exception $e) {
                                // エラーが発生しても無視する
                            }
                        }
                    }

                    log_info('商品CSV登録完了');
                    $message = 'admin.common.csv_upload_complete';
                    $this->session->getFlashBag()->add('eccube.admin.success', $message);

                    $cacheUtil->clearDoctrineCache();
                }
            }
        }

        return $this->renderWithError($form, $headers);
    }

    /**
     * カテゴリ登録CSVアップロード
     *
     * @Route("/%eccube_admin_route%/product/category_csv_upload", name="admin_product_category_csv_import")
     * @Template("@admin/Product/csv_category.twig")
     */
    public function csvCategory(Request $request, CacheUtil $cacheUtil)
    {
        $form = $this->formFactory->createBuilder(CsvImportType::class)->getForm();

        $headers = $this->getCategoryCsvHeader();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $formFile = $form['import_file']->getData();
                if (!empty($formFile)) {
                    log_info('カテゴリCSV登録開始');
                    $data = $this->getImportData($formFile);
                    if ($data === false) {
                        $this->addErrors(trans('admin.common.csv_invalid_format'));

                        return $this->renderWithError($form, $headers, false);
                    }

                    $getId = function ($item) {
                        return $item['id'];
                    };
                    $requireHeader = array_keys(array_map($getId, array_filter($headers, function ($value) {
                        return $value['required'];
                    })));

                    $headerByKey = array_flip(array_map($getId, $headers));

                    $columnHeaders = $data->getColumnHeaders();
                    if (count(array_diff($requireHeader, $columnHeaders)) > 0) {
                        $this->addErrors(trans('admin.common.csv_invalid_format'));

                        return $this->renderWithError($form, $headers, false);
                    }

                    $size = count($data);
                    if ($size < 1) {
                        $this->addErrors(trans('admin.common.csv_invalid_no_data'));

                        return $this->renderWithError($form, $headers, false);
                    }
                    $this->entityManager->getConfiguration()->setSQLLogger(null);
                    $this->entityManager->getConnection()->beginTransaction();
                    // CSVファイルの登録処理
                    foreach ($data as $row) {
                        /** @var $Category Category */
                        $Category = new Category();
                        if (isset($row[$headerByKey['id']]) && strlen($row[$headerByKey['id']]) > 0) {
                            if (!preg_match('/^\d+$/', $row[$headerByKey['id']])) {
                                $this->addErrors(($data->key() + 1).'行目のカテゴリIDが存在しません。');

                                return $this->renderWithError($form, $headers);
                            }
                            $Category = $this->categoryRepository->find($row[$headerByKey['id']]);
                            if (!$Category) {
                                $this->addErrors(($data->key() + 1).'行目のカテゴリIDが存在しません。');

                                return $this->renderWithError($form, $headers);
                            }
                            if ($row[$headerByKey['id']] == $row[$headerByKey['parent_category_id']]) {
                                $this->addErrors(($data->key() + 1).'行目のカテゴリIDと親カテゴリIDが同じです。');

                                return $this->renderWithError($form, $headers);
                            }
                        }

                        if (isset($row[$headerByKey['category_del_flg']]) && StringUtil::isNotBlank($row[$headerByKey['category_del_flg']])) {
                            if (StringUtil::trimAll($row[$headerByKey['category_del_flg']]) == 1) {
                                if ($Category->getId()) {
                                    log_info('カテゴリ削除開始', [$Category->getId()]);
                                    try {
                                        $this->categoryRepository->delete($Category);
                                        log_info('カテゴリ削除完了', [$Category->getId()]);
                                    } catch (ForeignKeyConstraintViolationException $e) {
                                        log_info('カテゴリ削除エラー', [$Category->getId(), $e]);
                                        $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $Category->getName()]);
                                        $this->addError($message, 'admin');

                                        return $this->renderWithError($form, $headers);
                                    }
                                }

                                continue;
                            }
                        }

                        if (!isset($row[$headerByKey['category_name']]) || StringUtil::isBlank($row[$headerByKey['category_name']])) {
                            $this->addErrors(($data->key() + 1).'行目のカテゴリ名が設定されていません。');

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Category->setName(StringUtil::trimAll($row[$headerByKey['category_name']]));
                        }

                        $ParentCategory = null;
                        if (isset($row[$headerByKey['parent_category_id']]) && StringUtil::isNotBlank($row[$headerByKey['parent_category_id']])) {
                            if (!preg_match('/^\d+$/', $row[$headerByKey['parent_category_id']])) {
                                $this->addErrors(($data->key() + 1).'行目の親カテゴリIDが存在しません。');

                                return $this->renderWithError($form, $headers);
                            }

                            /** @var $ParentCategory Category */
                            $ParentCategory = $this->categoryRepository->find($row[$headerByKey['parent_category_id']]);
                            if (!$ParentCategory) {
                                $this->addErrors(($data->key() + 1).'行目の親カテゴリIDが存在しません。');

                                return $this->renderWithError($form, $headers);
                            }
                        }
                        $Category->setParent($ParentCategory);

                        // Level
                        if (isset($row['階層']) && StringUtil::isNotBlank($row['階層'])) {
                            if ($ParentCategory == null && $row['階層'] != 1) {
                                $this->addErrors(($data->key() + 1).'行目の親カテゴリIDが存在しません。');

                                return $this->renderWithError($form, $headers);
                            }
                            $level = StringUtil::trimAll($row['階層']);
                        } else {
                            $level = 1;
                            if ($ParentCategory) {
                                $level = $ParentCategory->getHierarchy() + 1;
                            }
                        }

                        $Category->setHierarchy($level);

                        if ($this->eccubeConfig['eccube_category_nest_level'] < $Category->getHierarchy()) {
                            $this->addErrors(($data->key() + 1).'行目のカテゴリが最大レベルを超えているため設定できません。');

                            return $this->renderWithError($form, $headers);
                        }

                        if ($this->hasErrors()) {
                            return $this->renderWithError($form, $headers);
                        }
                        $this->entityManager->persist($Category);
                        $this->categoryRepository->save($Category);
                    }

                    $this->entityManager->getConnection()->commit();
                    log_info('カテゴリCSV登録完了');
                    $message = 'admin.common.csv_upload_complete';
                    $this->session->getFlashBag()->add('eccube.admin.success', $message);

                    $cacheUtil->clearDoctrineCache();
                }
            }
        }

        return $this->renderWithError($form, $headers);
    }

    /**
     * 追加項目の情報を追加している。
     * 
     * 商品登録CSVヘッダー定義
     *
     * @return array
     */
    protected function getProductCsvHeader()
    {
        if (!empty($this->cacheProductHeaders)) {
            return $this->cacheProductHeaders;
        }
        $headers = parent::getProductCsvHeader();
        
        $ProductColumns = $this->productColumnRepository->findAll();
        if (empty($ProductColumns)) {
            return $headers;
        }

        /**
         * @var ProductColumn $ProductColumn
         */
        foreach ($ProductColumns as $ProductColumn) {
            $name = $ProductColumn->getName();
            $headers[$name] = [
                'id' => $ProductColumn->getId(),
                'description' => $this->getDescription($ProductColumn),
                'required' => false,
            ];
            $this->cacheExProductHeaders[$name] = $headers[$name];
        }

        $this->cacheProductHeaders = $headers;
        return $this->cacheProductHeaders;
    }

    /**
     * Description の作成
     * @param ProductColumn $ProductColumn
     * @return string
     */
    function getDescription(ProductColumn $ProductColumn)
    {
        $description = '';
        if ($ProductColumn->getIsMultiple()) {
            $description .= trans('sheeb_expand_product_column.admin.product.csv.multiple', [
                '%separator%' => $this->eccubeConfig['eccube_csv_export_multidata_separator']
            ]);
        }

        switch ($ProductColumn->getType()) {
            case 'images':
                $description .= trans('sheeb_expand_product_column.admin.product.csv.images', [
                    '%path%' => $this->eccubeConfig['eccube_save_image_dir']
                ]);
                break;
            case 'checkbox':
                $description .= trans('sheeb_expand_product_column.admin.product.csv.checkbox', [
                    '%separator%' => CheckBoxType::CSV_GROUPING_SEPARATOR
                ]);
                // break しない
            case 'selectbox':
            case 'radiobutton':
                $setting = ColumnValueType::settingToArray($ProductColumn->getSetting());
                $choices = [];
                foreach ($setting as $key => $value) {
                    $choices[] = $value;
                }
                $description .= trans('sheeb_expand_product_column.admin.product.csv.settings', [
                    '%choices%' => implode(', ', $choices)
                ]);
                break;
        }
        
        return $description;
    }


    /**
     * @param Product $Product
     * @param $row
     * @throws \Doctrine\ORM\ORMException
     */
    private function editColumnValue(Product $Product, $row)
    {
        if (empty($this->cacheExProductHeaders)) {
            return;
        }

        // 一度この商品に関する情報全部削除
        $delColumnValues = $this->columnValueRepository->findBy([
            'Product' => $Product,
        ]);
        if (!empty($delColumnValues)) {
            $this->columnValueRepository->remove($delColumnValues);
        }

        $ProductColumns = $this->getProductColumns($this->cacheExProductHeaders);
        foreach ($this->cacheExProductHeaders as $headerName => $header) {
            if (!isset($row[$headerName]) || !isset($ProductColumns[$headerName])) {
                continue;
            }

            $ProductColumn = $ProductColumns[$headerName];
            $values = $row[$headerName];

            if ($ProductColumn->getIsMultiple()) {
                $values = explode($this->eccubeConfig['eccube_csv_export_multidata_separator'], $values);
            } else {
                $values = [$values];
            }

            $sequence = 0;
            foreach ($values as $value) {
                if ($ProductColumn->getType() === 'checkbox') {
                    // 空白を全て取り除く
                    $value = preg_replace("/( |　)/", "", $value );
                    $value = str_replace(
                        CheckBoxType::CSV_GROUPING_SEPARATOR,
                        CheckBoxType::ARRAY_SEPARATOR,
                        $value
                    );
                }

                // 選択肢の確認
                if (!empty($value) && in_array($ProductColumn->getType(), ['checkbox', 'selectbox', 'radiobutton'])) {
                    $setting = ColumnValueType::settingToArray($ProductColumn->getSetting());
                    $choices = [];
                    foreach ($setting as $key => $choice) {
                        $choices[] = $choice;
                    }

                    $isExistChoices = [$value];
                    if ($ProductColumn->getType() === 'checkbox') {
                        $isExistChoices = explode(CheckBoxType::ARRAY_SEPARATOR, $value);
                    }
                    foreach ($isExistChoices as $isExistChoice) {
                        if (!in_array($isExistChoice, $choices)) {
                            $message = trans('sheeb_expand_product_column.admin.product.csv.illegal_choice', ['%choice%' => $isExistChoice]);
                            $this->addErrors($message);
                        }
                    }
                }

                $ColumnValue = $this->columnValueRepository->findOneBy([
                    'Product' => $Product,
                    'ProductColumn' => $ProductColumn,
                    'sequence' => $sequence,
                ]);
                if (empty($ColumnValue)) {
                    $ColumnValue = new ColumnValue();
                    $ColumnValue->setProduct($Product);
                    $ColumnValue->setProductColumn($ProductColumn);
                    $ColumnValue->setSequence($sequence);
                }
                $ColumnValue->setValue($value);
                $this->columnValueRepository->save($ColumnValue, false);
                $sequence++;
            }

        }
    }

    /**
     * @param $exProductHeaders
     * @return ProductColumn[]
     */
    private function getProductColumns($exProductHeaders)
    {
        if (!is_null($this->ProductColumns)) {
            return $this->ProductColumns;
        }

        $headerNames = array_keys($exProductHeaders);

        $ProductColumns = $this->productColumnRepository->findAll();
        return array_reduce($ProductColumns, function ($reduced, ProductColumn $ProductColumn) use ($headerNames) {
            if (in_array($ProductColumn->getName(), $headerNames)) {
                $reduced[$ProductColumn->getName()] = $ProductColumn;
            }
            return $reduced;
        }, []);
    }
}