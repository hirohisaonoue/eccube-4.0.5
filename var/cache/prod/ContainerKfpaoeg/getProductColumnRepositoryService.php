<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'Plugin\SheebExpandProductColumn\Repository\ProductColumnRepository' shared autowired service.

include_once $this->targetDirs[3].'\\app\\Plugin\\SheebExpandProductColumn\\Repository\\ProductColumnRepository.php';

return $this->services['Plugin\\SheebExpandProductColumn\\Repository\\ProductColumnRepository'] = new \Plugin\SheebExpandProductColumn\Repository\ProductColumnRepository(${($_ = isset($this->services['doctrine']) ? $this->services['doctrine'] : $this->getDoctrineService()) && false ?: '_'});