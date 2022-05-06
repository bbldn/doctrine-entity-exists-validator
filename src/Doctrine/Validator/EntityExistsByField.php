<?php

namespace BBLDN\EntityExistsValidatorBundle\Doctrine\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class EntityExistsByField extends Constraint
{
    public string $entityClass;

    public ?string $em = null;

    public string $field = 'id';

    public bool $ignoreNull = true;

    public bool $ignoreLessThanOne = true;

    public string $repositoryMethod = 'findBy';

    public const NOT_EXISTS_ENTITY_BY_FIELD_ERROR = '07c80b0c-7a51-44b3-94dd-83bbb420ced2';

    public string $message = 'An "{{ entity }}" with the following field: "{{ field }}" does not exist';

    protected static $errorNames = [
        self::NOT_EXISTS_ENTITY_BY_FIELD_ERROR => 'NOT_EXISTS_ENTITY_BY_FIELD_ERROR',
    ];

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /**
     * @return string[]
     *
     * @psalm-return list<string>
     */
    public function getRequiredOptions(): array
    {
        return ['field', 'entityClass'];
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return EntityExistsByFieldValidator::class;
    }

    /**
     * @param string $field
     * @param string $message
     * @param string|null $em
     * @param bool $ignoreNull
     * @param string $entityClass
     * @param bool $ignoreLessThanOne
     * @param string $repositoryMethod
     */
    public function __construct(
        string $entityClass,
        ?string $em = null,
        string $field = 'id',
        bool $ignoreNull = true,
        bool $ignoreLessThanOne = true,
        string $repositoryMethod  = 'findBy',
        string $message = 'An "{{ entity }}" with the following field: "{{ field }}" does not exist'
    )
    {
        $this->em = $em;
        $this->field = $field;
        $this->message = $message;
        $this->ignoreNull = $ignoreNull;
        $this->entityClass = $entityClass;
        $this->repositoryMethod = $repositoryMethod;
        $this->ignoreLessThanOne = $ignoreLessThanOne;

        parent::__construct();
    }
}