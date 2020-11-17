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
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Csv;

/**
 * Class ProductColumn
 * 
 * @ORM\Table(name="plg_sheeb_expand_product_column")
 * @ORM\Entity(repositoryClass="Plugin\SheebExpandProductColumn\Repository\ProductColumnRepository")
 */
class ProductColumn extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @Assert\Regex("/^[a-zA-Z0-9\_]+$/")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=16)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="is_multiple", type="smallint")
     */
    private $isMultiple;

    /**
     * @var int
     *
     * @ORM\Column(name="is_searchable", type="smallint")
     */
    private $isSearchable;

    /**
     * @var string
     *
     * @ORM\Column(name="setting", type="text", nullable=true)
     */
    private $setting;

    /**
     * @var Csv
     *
     * @ORM\OneToOne(targetEntity="Eccube\Entity\Csv")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="csv_id", referencedColumnName="id")
     * })
     */
    private $Csv;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsMultiple()
    {
        return $this->isMultiple;
    }

    /**
     * @param $isMultiple
     * @return $this
     */
    public function setIsMultiple($isMultiple)
    {
        $this->isMultiple = $isMultiple;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsSearchable()
    {
        return $this->isSearchable;
    }

    /**
     * @param $isSearchable
     * @return $this
     */
    public function setIsSearchable($isSearchable)
    {
        $this->isSearchable = $isSearchable;
        return $this;
    }

    /**
     * @return string
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * @param $setting
     * @return $this
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;
        return $this;
    }

    /**
     * @return Csv
     */
    public function getCsv()
    {
        return $this->Csv;
    }

    /**
     * @param $Csv
     * @return $this
     */
    public function setCsv($Csv)
    {
        $this->Csv = $Csv;
        return $this;
    }
}