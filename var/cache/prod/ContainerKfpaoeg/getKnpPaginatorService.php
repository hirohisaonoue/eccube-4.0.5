<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'knp_paginator' shared service.

include_once $this->targetDirs[3].'\\vendor\\knplabs\\knp-components\\src\\Knp\\Component\\Pager\\PaginatorInterface.php';
include_once $this->targetDirs[3].'\\vendor\\knplabs\\knp-components\\src\\Knp\\Component\\Pager\\Paginator.php';

if ($lazyLoad) {
    return $this->services['knp_paginator'] = $this->createProxy('Paginator_f262b94', function () {
        return \Paginator_f262b94::staticProxyConstructor(function (&$wrappedInstance, \ProxyManager\Proxy\LazyLoadingInterface $proxy) {
            $wrappedInstance = $this->load('getKnpPaginatorService.php', false);

            $proxy->setProxyInitializer(null);

            return true;
        });
    });
}

$instance = new \Knp\Component\Pager\Paginator(${($_ = isset($this->services['event_dispatcher']) ? $this->services['event_dispatcher'] : $this->getEventDispatcherService()) && false ?: '_'});

$instance->setDefaultPaginatorOptions(['pageParameterName' => 'page', 'sortFieldParameterName' => 'sort', 'sortDirectionParameterName' => 'direction', 'filterFieldParameterName' => 'filterField', 'filterValueParameterName' => 'filterValue', 'distinct' => true]);

return $instance;
