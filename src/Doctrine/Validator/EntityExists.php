<?php

namespace BBLDN\EntityExistsValidatorBundle\Doctrine\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class EntityExists extends Constraint
{
    public ?string $em = null;

    /** @var string[] */
    public array $fields = [];

    public string $entityClass;

    public bool $ignoreNull = true;

    public string $errorPath = 'Entity';

    public bool $ignoreLessThanOne = true;

    public string $repositoryMethod = 'findBy';

    public const NOT_EXISTS_ENTITY_ERROR = '2c1787b7-886e-4b66-a59c-e86ccdddae52';

    public string $message = 'An "{{ entity }}" with the following fields: "{{ fields }}" does not exist';

    protected static $errorNames = [
        self::NOT_EXISTS_ENTITY_ERROR => 'NOT_EXISTS_ENTITY_ERROR',
    ];

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return EntityExistsValidator::class;
    }

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return ['fields', 'entityClass'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}