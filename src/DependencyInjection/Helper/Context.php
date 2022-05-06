<?php

namespace BBLDN\EntityExistsValidatorBundle\DependencyInjection\Helper;

class Context
{
    private string $extensionTag = 'bbldn.doctrine_entity_exists_validator';

    private string $validatorConstraintValidatorTag = 'validator.constraint_validator';

    /**
     * @return string
     */
    public function getExtensionTag(): string
    {
        return $this->extensionTag;
    }

    /**
     * @return string
     */
    public function getValidatorConstraintValidatorTag(): string
    {
        return $this->validatorConstraintValidatorTag;
    }
}