<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'Eccube\Command\PluginGenerateCommand' shared autowired service.

include_once $this->targetDirs[3].'\\vendor\\symfony\\console\\Command\\Command.php';
include_once $this->targetDirs[3].'\\src\\Eccube\\Command\\PluginGenerateCommand.php';

$this->services['Eccube\\Command\\PluginGenerateCommand'] = $instance = new \Eccube\Command\PluginGenerateCommand($this);

$instance->setName('eccube:plugin:generate');

return $instance;
