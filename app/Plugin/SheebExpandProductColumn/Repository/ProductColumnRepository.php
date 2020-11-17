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

use Eccube\Repository\AbstractRepository;
use Plugin\SheebExpandProductColumn\Entity\ProductColumn;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ProductColumn
 */
class ProductColumnRepository extends AbstractRepository
{
    /**
     * ProductReviewRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductColumn::class);
    }
}