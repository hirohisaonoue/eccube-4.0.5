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

namespace Plugin\SheebExpandProductColumn\Repository;

use Doctrine\ORM\UnitOfWork;
use Eccube\Entity\Product;
use Eccube\Form\Type\SearchProductType;
use Eccube\Repository\AbstractRepository;
use Eccube\Repository\ProductRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Plugin\SheebExpandProductColumn\Entity\ColumnValue;
use Plugin\SheebExpandProductColumn\Entity\ProductColumn;
use Plugin\SheebExpandProductColumn\Event;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ColumnValueRepository
 */
class ColumnValueRepository extends AbstractRepository
{
    /**
     * @var FormFactory
     */
    private $formFactory;
    
    /**
     * @var ProductRepository
     */
    private $productRepository;
    
    /**
     * @var ProductColumnRepository
     */
    private $productColumnRepository;

    /**
     * @param FormFactory $formFactory
     * @param RegistryInterface $registry
     * @param ProductRepository $productRepository
     * @param ProductColumnRepository $productColumnRepository
     */
    public function __construct(
        FormFactory $formFactory,
        RegistryInterface $registry,
        ProductRepository $productRepository,
        ProductColumnRepository $productColumnRepository)
    {
        parent::__construct($registry, ColumnValue::class);
        $this->formFactory = $formFactory;
        $this->productRepository = $productRepository;
        $this->productColumnRepository = $productColumnRepository;
    }

    /**
     * @param $ColumnValues
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove($ColumnValues)
    {
        foreach ($ColumnValues as $ColumnValue) {
            $state = $this->getEntityManager()->getUnitOfWork()->getEntityState($ColumnValue);
            if ($state === UnitOfWork::STATE_DETACHED) {
                continue;
            }
            $this->getEntityManager()->remove($ColumnValue);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param ColumnValue $entity
     * @param bool $repairSequence
     */
    public function save($entity, $repairSequence = true)
    {
        if ($entity->getProduct()->getId() < 1) {
            // FIXME: $_SESSION を直接いじっている (rel: $this->saveBySession でも同様)
            // 新規追加時には ProductID がわからないので SESSION に保存
            $_SESSION
                [Event::SESSION_KEY]
                [$entity->getProductColumn()->getId()]
                [$entity->getSequence()] = $entity->getValue();
            return;
        }
        
        // 保存
        parent::save($entity);

        if (!$repairSequence) {
            return;
        }
        
        // シーケンスの順序を補正する
        $ColumnValues = $this->findBy(
            [
                'Product' => $entity->getProduct(),
                'ProductColumn' => $entity->getProductColumn()
            ],
            ['sequence' => 'ASC']
        );

        /**
         * @var ColumnValue $ColumnValue
         */
        $sequence = 0;
        foreach ($ColumnValues as $ColumnValue) {
            parent::save($ColumnValue->setSequence($sequence));
            $sequence++;
        }
    }

    /**
     * FIXME: $_SESSION を直接いじっている (rel: $this->save でも同様)
     * @param Product $Product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveBySession(Product $Product)
    {
        if (empty($Product->getId())) {
            return;
        }
        if (!isset($_SESSION[Event::SESSION_KEY])) {
            return;
        }
        if (!is_array($_SESSION[Event::SESSION_KEY])) {
            return;
        }
        
        $session = $_SESSION[Event::SESSION_KEY];
        
        $ProductColumns = $this->productColumnRepository->findById(array_keys($session));
        $ProductColumns = array_reduce($ProductColumns, function ($reduced, ProductColumn $ProductColumn) {
            $reduced[$ProductColumn->getId()] = $ProductColumn;
            return $reduced;
        }, []);

        $entities = [];
        foreach ($session as $columnId => $values) {
            if (!isset($ProductColumns[$columnId])) {
                continue;
            }
            foreach ($values as $sequence => $value) {
                $ColumnValue = new ColumnValue();
                $ColumnValue->setProduct($Product);
                $ColumnValue->setProductColumn($ProductColumns[$columnId]);
                $ColumnValue->setSequence($sequence);
                $ColumnValue->setValue($value);
                $this->save($ColumnValue);
                $entities[] = $ColumnValue;
            }
        }
        
        $this->getEntityManager()->flush($entities);
        unset($_SESSION[Event::SESSION_KEY]);
    }
    
    /* *******************************
     *          Twig変数用
     * *******************************/

    /**
     * @param Product|int $Product
     * @return array
     */
    public function getExProduct($Product)
    {
        if (is_int($Product)) {
            $Product = $this->productRepository->find($Product);
        }

        if (!($Product instanceof Product)) {
            return [];
        }

        $values = $this->assortByCode($this->findBy(
            ['Product' => $Product],
            ['ProductColumn' => 'ASC']
        ));

        if (!isset($values[$Product->getId()])) {
            log_warning('商品情報追加プラグイン: 存在しない Product');
            return [];
        }

        return $values[$Product->getId()];
    }

    /**
     * @param $productId
     * @return null|object
     */
    public function getProduct($productId)
    {
        return $this->productRepository->find($productId);
    }

    /**
     * @param array $Condition
     * @return array
     */
    public function getProductList($Condition = [])
    {
        $firstCondition = null;
        $isAssociativeArray = false;
        if (is_array($Condition)) {
            $firstCondition = reset($Condition);
            if (is_string(key($Condition))) {
                $isAssociativeArray = true;
            };
        }

        // Condition の内容によって取得する対象の Product を変える
        $Products = [];
        $success = true;
        $message = '';
        try {
            // 1. 空っぽ = 全Product取得
            if (empty($Condition)) {
                $Products = $this->productRepository->findAll();
            }
            // Pagination = Pagination内のProduct
            else if ($Condition instanceof PaginationInterface) {
                $Products = [];
                foreach ($Condition as $Product) {
                    if (!($Product instanceof Product)) {
                        continue;
                    }
                    $Products[] = $Product;
                }
            }
            // 2. Product[] = 指定されたProduct
            else if ($firstCondition instanceof Product) {
                $Products = [];
                foreach ($Condition as $Product) {
                    if (!($Product instanceof Product)) {
                        continue;
                    }
                    $Products[] = $Product;
                }
            }
            // 3. Product = 指定されたProduct
            else if ($Condition instanceof Product) {
                $Products[] = $Condition;
            }
            // 4. 数値 = ProductIDで取得されるProduct
            else if (is_int($Condition)) {
                $Products[] = $this->productRepository->find($Condition);
            }
            // 5. 数値の配列 = ProductIDで取得されるProduct
            else if (is_int($firstCondition) && !$isAssociativeArray) {
                $Products = $this->productRepository->findById($Condition);
            }
            // 6. それ以外の配列 = ProductRepository->findBy の引数として利用して取得されるProduct
            else if (is_array($Condition) && $isAssociativeArray) {
                $builder = $this->formFactory->createNamedBuilder('', SearchProductType::class);
                $builder->setAttribute('freeze', true);
                $builder->setAttribute('freeze_display_text', false);
                $builder->setMethod('GET');

                $form = $builder->getForm();
                $form->handleRequest(Request::create('', 'GET', $Condition));

                $Products = $this->productRepository->getQueryBuilderBySearchData(
                    $form->getData()
                )->getQuery()->getResult();
            }
            // 7. それ以外は認めない
            else {
                throw new \Exception('有効な Condition ではありません');
            }
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
        }

        if (!$success) {
            log_warning('商品情報追加プラグイン: 不適切な条件が渡されました');
            log_warning('商品情報追加プラグイン: ' . $message);
            return [];
        }

        return $Products;
    }

    /**
     * @param array $Condition
     * @return mixed
     */
    public function getExProductList($Condition = [])
    {
        $Products = $this->getProductList($Condition);

        if (empty($Products)) {
            return [];
        }
        
        $values = $this->assortByCode($this->findBy(
            ['Product' => $Products],
            ['ProductColumn' => 'ASC']
        ));

        foreach ($Products as $Product) {
            if (!isset($values[$Product->getId()])) {
                $values[$Product->getId()] = [];
            }
        }

        return $values;
    }

    /**
     * @param $ColumnValues
     * @return mixed
     */
    private function assortByCode($ColumnValues)
    {
        return array_reduce($ColumnValues, function ($reduced, ColumnValue $ColumnValue) {
            $ProductColumn = $ColumnValue->getProductColumn();

            if ($ProductColumn->getIsMultiple()) {
                $reduced
                [$ColumnValue->getProduct()->getId()]
                [$ProductColumn->getCode()]
                [$ColumnValue->getSequence()] = $ColumnValue->getValue();
            } else {
                $reduced
                [$ColumnValue->getProduct()->getId()]
                [$ProductColumn->getCode()] = $ColumnValue->getValue();
            }

            return $reduced;
        }, []);
    }

}