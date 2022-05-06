<?php

namespace BBLDN\EntityExistsValidatorBundle\Doctrine\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_CLASS)]
class EntityExists extends Constraint
{
    public string $entityClass;

    public ?string $em = null;

    /**
     * @var string[]
     *
     * @psalm-var list<string>
     */
    public array $fields = [];

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
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return EntityExistsValidator::class;
    }

    /**
     * @return string[]
     *
     * @psalm-return list<string>
     */
    public function getRequiredOptions(): array
    {
        return ['fields', 'entityClass'];
    }

    /**
     * @param array $fields
     * @param string $message
     * @param string|null $em
     * @param bool $ignoreNull
     * @param string $errorPath
     * @param string $entityClass
     * @param bool $ignoreLessThanOne
     * @param string $repositoryMethod
     */
    public function __construct(
        string $entityClass,
        ?string $em = null,
        array $fields = [],
        bool $ignoreNull = true,
        string $errorPath = 'Entity',
        bool $ignoreLessThanOne = true,
        string $repositoryMethod  = 'findBy',
        string $message = 'An "{{ entity }}" with the following fields: "{{ fields }}" does not exist'
    )
    {
        $this->em = $em;
        $this->fields = $fields;
        $this->message = $message;
        $this->errorPath = $errorPath;
        $this->ignoreNull = $ignoreNull;
        $this->entityClass = $entityClass;
        $this->repositoryMethod = $repositoryMethod;
        $this->ignoreLessThanOne = $ignoreLessThanOne;

        $options = [
            'fields' => $this->fields,
            'entityClass' => $this->entityClass,
        ];

        parent::__construct($options);
    }
}