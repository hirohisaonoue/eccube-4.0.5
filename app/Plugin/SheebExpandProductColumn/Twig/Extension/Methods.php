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

namespace Plugin\SheebExpandProductColumn\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Product;
use Plugin\SheebExpandProductColumn\Entity\ColumnValue;
use Plugin\SheebExpandProductColumn\Repository\ColumnValueRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Methods extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * @var ColumnValueRepository
     */
    private $columnValueRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->columnValueRepository = $em->getRepository(ColumnValue::class);
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getPageUri', array($this, 'getPageUri')),
            new TwigFunction('getDomain', array($this, 'getDomain')),
            new TwigFunction('exValue', array($this, 'exValue')),
            new TwigFunction('exArrayValue', array($this, 'exArrayValue')),
            new TwigFunction('getProduct', array($this, 'getProduct')),
            new TwigFunction('getExProduct', array($this, 'getExProduct')),
            new TwigFunction('getProductList', array($this, 'getProductList')),
            new TwigFunction('getExProductList', array($this, 'getExProductList')),
        ];
    }

    /**
     * @return mixed
     */
    public function getPageUri()
    {
        return $this->getDomain() . $_SERVER['REQUEST_URI'];
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        $host = $_SERVER['HTTP_HOST'];
        return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$host}";
    }

    /**
     * 表示用メソッド
     * @param $EX_PRODUCT
     * @param $code
     * @return string
     */
    public function exValue($EX_PRODUCT, $code)
    {
        if (!isset($EX_PRODUCT[$code])) {
            return '';
        }

        return $EX_PRODUCT[$code];
    }

    /**
     * 配列表示用メソッド
     * @param $EX_PRODUCT
     * @param $code
     * @param string $mode
     * @return array|string
     */
    public function exArrayValue($EX_PRODUCT, $code, $mode = 'array')
    {
        $array = $this->exValue($EX_PRODUCT, $code);

        if (!is_array($array)) {
            return $mode === 'array' ? [] : $array;
        }

        if ($mode === 'array') {
            return $array;
        }
        
        return $this->format($array, $mode);
    }

    /**
     * @param $values
     * @param string $mode
     * @return string
     */
    private function format($values, $mode = 'p')
    {
        switch ($mode) {
            case 'p':
                $result = $this->surround($values, $mode);
                break;
            case ',':
                $result = implode(', ', $values);
                break;
            default:
                $result = implode(', ', $values);
                break;
        }
        
        return $result;
    }

    /**
     * @param $values
     * @param string $mode
     * @return string
     */
    private function surround($values, $mode = 'p')
    {
        $source = '';
        array_walk_recursive($values, function ($value, $key, $mode) use (&$source) {
            switch ($mode) {
                case 'p':
                    $source .= "<p>{$value}</p>";
                    break;
                default:
                    $source .= $value;
                    break;
            }
        }, $mode);
        
        
        return $source;
    }

    /**
     * @param $productId
     * @return null|object
     */
    public function getProduct($productId)
    {
        return $this->columnValueRepository->getProduct($productId);
    }

    /**
     * @param Product|int $Product
     * @return array
     */
    public function getExProduct($Product)
    {
        return $this->columnValueRepository->getExProduct($Product);
    }

    /**
     * @param array $Condition
     * @return mixed
     */
    public function getProductList($Condition = [])
    {
        return $this->columnValueRepository->getProductList($Condition);
    }

    /**
     * @param array $Condition
     * @return mixed
     */
    public function getExProductList($Condition = [])
    {
        return $this->columnValueRepository->getExProductList($Condition);
    }
}
