<?php

namespace BBLDN\Doctrine\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class EntityExistsByField extends Constraint
{
    public ?string $em = null;

    public string $entityClass;

    public string $field = 'id';

    public bool $ignoreNull = true;

    public bool $ignoreLessThanOne = true;

    public string $repositoryMethod = 'findBy';

    public const NOT_EXISTS_ENTITY_BY_FIELD_ERROR = '07c80b0c-7a51-44b3-94dd-83bbb420ced2';

    public string $message = 'An "{{ entity }}" with the following field: "{{ field }}" does not exist';

    /** @var array<string, string> */
    protected static $errorNames = [
        self::NOT_EXISTS_ENTITY_BY_FIELD_ERROR => 'NOT_EXISTS_ENTITY_BY_FIELD_ERROR',
    ];

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return EntityExistsByFieldValidator::class;
    }

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return ['field', 'entityClass'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}