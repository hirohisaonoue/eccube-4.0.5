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

namespace Plugin\SheebExpandProductColumn\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Product;

/**
 * Class ProductColumn
 * 
 * @ORM\Table(name="plg_sheeb_expand_product_column_value")
 * @ORM\Entity(repositoryClass="Plugin\SheebExpandProductColumn\Repository\ColumnValueRepository")
 */
class ColumnValue extends AbstractEntity
{
    /**
     * @var Product
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $Product;

    /**
     * @var ProductColumn
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Plugin\SheebExpandProductColumn\Entity\ProductColumn")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="column_id", referencedColumnName="id")
     * })
     */
    private $ProductColumn;

    /**
     * @var int
     * 
     * @ORM\Id
     * @ORM\Column(name="sequence", type="integer", options={"unsigned":true})
     */
    private $sequence;
    
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * @param $Product
     * @return $this
     */
    public function setProduct($Product)
    {
        $this->Product = $Product;
        return $this;
    }

    /**
     * @return ProductColumn
     */
    public function getProductColumn()
    {
        return $this->ProductColumn;
    }

    /**
     * @param $ProductColumn
     * @return $this
     */
    public function setProductColumn($ProductColumn)
    {
        $this->ProductColumn = $ProductColumn;
        return $this;
    }

    /**
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     *
     * @return $this
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}