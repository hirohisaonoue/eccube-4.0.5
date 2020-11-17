<?php

class Logger_fadf7f2 extends \Eccube\Log\Logger implements \ProxyManager\Proxy\VirtualProxyInterface
{
    private $valueHolderfadf7f2 = null;
    private $initializerfadf7f2 = null;
    private static $publicPropertiesfadf7f2 = [
        
    ];
    public function log($level, $message, array $context = [])
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'log', array('level' => $level, 'message' => $message, 'context' => $context), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return $this->valueHolderfadf7f2->log($level, $message, $context);
    }
    public function emergency($message, array $context = [])
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'emergency', array('message' => $message, 'context' => $context), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return $this->valueHolderfadf7f2->emergency($message, $context);
    }
    public function alert($message, array $context = [])
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'alert', array('message' => $message, 'context' => $context), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return $this->valueHolderfadf7f2->alert($message, $context);
    }
    public function critical($message, array $context = [])
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'critical', array('message' => $message, 'context' => $context), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return $this->valueHolderfadf7f2->critical($message, $context);
    }
    public function error($message, array $context = [])
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'error', array('message' => $message, 'context' => $context), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return $this->valueHolderfadf7f2->error($message, $context);
    }
    public function warning($message, array $context = [])
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'warning', array('message' => $message, 'context' => $context), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return $this->valueHolderfadf7f2->warning($message, $context);
    }
    public function notice($message, array $context = [])
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'notice', array('message' => $message, 'context' => $context), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return $this->valueHolderfadf7f2->notice($message, $context);
    }
    public function info($message, array $context = [])
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'info', array('message' => $message, 'context' => $context), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return $this->valueHolderfadf7f2->info($message, $context);
    }
    public function debug($message, array $context = [])
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'debug', array('message' => $message, 'context' => $context), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return $this->valueHolderfadf7f2->debug($message, $context);
    }
    public static function staticProxyConstructor($initializer)
    {
        static $reflection;
        $reflection = $reflection ?: $reflection = new \ReflectionClass(__CLASS__);
        $instance = (new \ReflectionClass(get_class()))->newInstanceWithoutConstructor();
        unset($instance->context, $instance->logger, $instance->frontLogger, $instance->adminLogger);
        $instance->initializerfadf7f2 = $initializer;
        return $instance;
    }
    public function __construct(\Eccube\Request\Context $context, \Psr\Log\LoggerInterface $logger, \Psr\Log\LoggerInterface $frontLogger, \Psr\Log\LoggerInterface $adminLogger)
    {
        static $reflection;
        if (! $this->valueHolderfadf7f2) {
            $reflection = $reflection ?: new \ReflectionClass('Eccube\\Log\\Logger');
            $this->valueHolderfadf7f2 = $reflection->newInstanceWithoutConstructor();
        unset($this->context, $this->logger, $this->frontLogger, $this->adminLogger);
        }
        $this->valueHolderfadf7f2->__construct($context, $logger, $frontLogger, $adminLogger);
    }
    public function & __get($name)
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, '__get', ['name' => $name], $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        if (isset(self::$publicPropertiesfadf7f2[$name])) {
            return $this->valueHolderfadf7f2->$name;
        }
        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));
        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderfadf7f2;
            $backtrace = debug_backtrace(false);
            trigger_error(
                sprintf(
                    'Undefined property: %s::$%s in %s on line %s',
                    get_parent_class($this),
                    $name,
                    $backtrace[0]['file'],
                    $backtrace[0]['line']
                ),
                \E_USER_NOTICE
            );
            return $targetObject->$name;
            return;
        }
        $targetObject = $this->valueHolderfadf7f2;
        $accessor = function & () use ($targetObject, $name) {
            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();
        return $returnValue;
    }
    public function __set($name, $value)
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, '__set', array('name' => $name, 'value' => $value), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));
        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderfadf7f2;
            return $targetObject->$name = $value;
            return;
        }
        $targetObject = $this->valueHolderfadf7f2;
        $accessor = function & () use ($targetObject, $name, $value) {
            return $targetObject->$name = $value;
        };
        $backtrace = debug_backtrace(true);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();
        return $returnValue;
    }
    public function __isset($name)
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, '__isset', array('name' => $name), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));
        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderfadf7f2;
            return isset($targetObject->$name);
            return;
        }
        $targetObject = $this->valueHolderfadf7f2;
        $accessor = function () use ($targetObject, $name) {
            return isset($targetObject->$name);
        };
        $backtrace = debug_backtrace(true);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();
        return $returnValue;
    }
    public function __unset($name)
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, '__unset', array('name' => $name), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        $realInstanceReflection = new \ReflectionClass(get_parent_class($this));
        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderfadf7f2;
            unset($targetObject->$name);
            return;
        }
        $targetObject = $this->valueHolderfadf7f2;
        $accessor = function () use ($targetObject, $name) {
            unset($targetObject->$name);
        };
        $backtrace = debug_backtrace(true);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();
        return $returnValue;
    }
    public function __clone()
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, '__clone', array(), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        $this->valueHolderfadf7f2 = clone $this->valueHolderfadf7f2;
    }
    public function __sleep()
    {
        $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, '__sleep', array(), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
        return array('valueHolderfadf7f2');
    }
    public function __wakeup()
    {
        unset($this->context, $this->logger, $this->frontLogger, $this->adminLogger);
    }
    public function setProxyInitializer(\Closure $initializer = null)
    {
        $this->initializerfadf7f2 = $initializer;
    }
    public function getProxyInitializer()
    {
        return $this->initializerfadf7f2;
    }
    public function initializeProxy() : bool
    {
        return $this->initializerfadf7f2 && ($this->initializerfadf7f2->__invoke($valueHolderfadf7f2, $this, 'initializeProxy', array(), $this->initializerfadf7f2) || 1) && $this->valueHolderfadf7f2 = $valueHolderfadf7f2;
    }
    public function isProxyInitialized() : bool
    {
        return null !== $this->valueHolderfadf7f2;
    }
    public function getWrappedValueHolderValue()
    {
        return $this->valueHolderfadf7f2;
    }
}
