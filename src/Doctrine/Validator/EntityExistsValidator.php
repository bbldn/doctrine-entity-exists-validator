<?php

namespace BBLDN\EntityExistsValidatorBundle\Doctrine\Validator;

use ReflectionException;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class EntityExistsValidator extends ConstraintValidator
{
    private ManagerRegistry $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param Constraint $constraint
     */
    private function validateConstraint(Constraint $constraint): void
    {
        if (false === is_a($constraint, EntityExists::class)) {
            throw new UnexpectedTypeException($constraint, EntityExists::class);
        }

        if (false === is_array($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        if (0 === count($constraint->fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }
    }

    /**
     * @param EntityExists $constraint
     * @return ObjectManager
     */
    private function getObjectManager(EntityExists $constraint): ObjectManager
    {
        if (null !== $constraint->em && '' !== $constraint->em) {
            $manager = $this->registry->getManager($constraint->em);

            if (null === $manager) {
                $m = "Object manager \"{$constraint->em}\" does not exist.";
                throw new ConstraintDefinitionException($m);
            }

            return $manager;
        }

        $manager = $this->registry->getManagerForClass($constraint->entityClass);
        if (null === $manager) {
            $m = "Unable to find the object manager associated with an entity of class \"{$constraint->entityClass}\".";
            throw new ConstraintDefinitionException($m);
        }

        return $manager;
    }

    /**
     * @param EntityWrapper $entityWrapper
     * @param EntityExists $constraint
     * @return bool
     * @throws ReflectionException
     */
    private function checkNullAndLessThanOne(EntityWrapper $entityWrapper, EntityExists $constraint): bool
    {
        foreach ($constraint->fields as $field) {
            $fieldValue = $entityWrapper->getValue($field);
            if (null === $fieldValue && true === $constraint->ignoreNull) {
                return false;
            }

            if ($fieldValue < 1 && true === $constraint->ignoreLessThanOne) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ObjectManager $manager
     * @param string $entityClass
     * @return ObjectRepository
     */
    private function getRepository(ObjectManager $manager, string $entityClass): ObjectRepository
    {
        $repository = $manager->getRepository($entityClass);
        $supportedClass = $repository->getClassName();
        if ($supportedClass !== $entityClass) {
            $m = "The \"{$entityClass}\" entity repository does not support the \"{$supportedClass}\" entity.";
            throw new ConstraintDefinitionException($m);
        }

        return $repository;
    }

    /**
     * @param EntityWrapper $entityWrapper
     * @param array $fields
     * @return array
     * @throws ReflectionException
     */
    private function getCriteria(EntityWrapper $entityWrapper, array $fields): array
    {
        $criteria = [];
        foreach ($fields as $field) {
            $criteria[$field] = $entityWrapper->getValue($field);
        }

        return $criteria;
    }

    /**
     * @param ObjectRepository $repository
     * @param string $method
     * @param array $criteria
     * @return int
     */
    private function getCountByFields(ObjectRepository $repository, string $method, array $criteria): int
    {
        $result = $repository->{$method}($criteria);

        return count($result);
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @throws ReflectionException
     */
    public function validate($value, Constraint $constraint): void
    {
        $this->validateConstraint($constraint);
        /** @var EntityExists $constraint */
        if ('' === $constraint->entityClass) {
            return;
        }

        $manager = $this->getObjectManager($constraint);
        $entityWrapper = new EntityWrapper($value);
        if (false === $this->checkNullAndLessThanOne($entityWrapper, $constraint)) {
            return;
        }

        $repository = $this->getRepository($manager, $constraint->entityClass);
        $criteria = $this->getCriteria($entityWrapper, $constraint->fields);
        $count = $this->getCountByFields($repository, $constraint->repositoryMethod, $criteria);

        if ($count > 0) {
            return;
        }

        $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : $constraint->fields[0];

        $this->context->buildViolation($constraint->message)
            ->atPath($errorPath)
            ->setInvalidValue($criteria)
            ->setCode(EntityExists::NOT_EXISTS_ENTITY_ERROR)
            ->setParameter('{{ entity }}', $constraint->entityClass)
            ->setParameter('{{ fields }}', implode(', ', $constraint->fields))
            ->addViolation();
    }
}