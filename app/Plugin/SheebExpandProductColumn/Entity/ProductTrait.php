<?php

namespace Plugin\SheebExpandProductColumn\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;
use Eccube\Annotation\EntityExtension;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @EntityExtension("Eccube\Entity\Product")
 */
trait ProductTrait
{
    /**
     * @var ColumnValue[]
     */
    private $ColumnValues;

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
     * @return ColumnValue[]
     */
    public function getColumnValues()
    {
        return $this->ColumnValues;
    }

    /**
     * @param $ColumnValues
     * @return $this
     */
    public function setColumnValues($ColumnValues)
    {
        $this->ColumnValues = $ColumnValues;
        return $this;
    }
}