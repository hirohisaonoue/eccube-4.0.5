<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'Plugin\SheebExpandProductColumn\Form\Type\Admin\ProductColumnConfigType' shared autowired service.

include_once $this->targetDirs[3].'\\vendor\\symfony\\form\\FormTypeInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\form\\AbstractType.php';
include_once $this->targetDirs[3].'\\app\\Plugin\\SheebExpandProductColumn\\Form\\Type\\Admin\\ProductColumnConfigType.php';

return $this->services['Plugin\\SheebExpandProductColumn\\Form\\Type\\Admin\\ProductColumnConfigType'] = new \Plugin\SheebExpandProductColumn\Form\Type\Admin\ProductColumnConfigType(${($_ = isset($this->services['Eccube\\Common\\EccubeConfig']) ? $this->services['Eccube\\Common\\EccubeConfig'] : ($this->services['Eccube\\Common\\EccubeConfig'] = new \Eccube\Common\EccubeConfig($this))) && false ?: '_'});
