<?php

namespace BBLDN\EntityExistsValidatorBundle\Doctrine\Validator;

use ReflectionClass;
use ReflectionProperty;
use ReflectionException;

class EntityWrapper
{
    private $instance;

    /**
     * @var ReflectionProperty[]
     *
     * @psalm-var list<ReflectionProperty>
     */
    private array $properties = [];

    private ReflectionClass $reflectionClass;

    /**
     * @param mixed $instance
     * @throws ReflectionException
     */
    public function __construct($instance)
    {
        $this->reflectionClass = new ReflectionClass(get_class($instance));
        $this->instance = $instance;
    }

    /**
     * @param string $propertyName
     * @return ReflectionProperty
     * @throws ReflectionException
     */
    private function getReflectionProperty(string $propertyName): ReflectionProperty
    {
        if (true === key_exists($propertyName, $this->properties)) {
            return $this->properties[$propertyName];
        }

        $reflectionProperty = $this->reflectionClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        $this->properties[$propertyName] = $reflectionProperty;

        return $reflectionProperty;
    }

    /**
     * @param string $propertyName
     * @return mixed
     * @throws ReflectionException
     */
    public function getValue(string $propertyName)
    {
        return $this->getReflectionProperty($propertyName)->getValue($this->instance);
    }
}