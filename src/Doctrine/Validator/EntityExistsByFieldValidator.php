<?php

namespace BBLDN\EntityExistsValidatorBundle\Doctrine\Validator;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class EntityExistsByFieldValidator extends ConstraintValidator
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
     * @return void
     */
    private function validateConstraint(Constraint $constraint): void
    {
        if (false === is_a($constraint, EntityExistsByField::class)) {
            throw new UnexpectedTypeException($constraint, EntityExistsByField::class);
        }
    }

    /**
     * @param EntityExistsByField $constraint
     * @return ObjectManager
     */
    private function getObjectManager(EntityExistsByField $constraint): ObjectManager
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
     * @param mixed $value
     * @param EntityExistsByField $constraint
     * @return bool
     */
    private function checkNullAndLessThanOne($value, EntityExistsByField $constraint): bool
    {
        if (true === $constraint->ignoreNull && null === $value) {
            return false;
        }

        if (true === $constraint->ignoreLessThanOne && true === is_numeric($value)) {
            if ($value < 1) {
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
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        $this->validateConstraint($constraint);
        /** @var EntityExistsByField $constraint */
        if ('' === $constraint->entityClass) {
            return;
        }

        if (false === $this->checkNullAndLessThanOne($value, $constraint)) {
            return;
        }

        $criteria = [$constraint->field => $value];
        $manager = $this->getObjectManager($constraint);
        $repository = $this->getRepository($manager, $constraint->entityClass);
        $count = $this->getCountByFields($repository, $constraint->repositoryMethod, $criteria);

        if ($count > 0) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setInvalidValue($criteria)
            ->setParameter('{{ field }}', $constraint->field)
            ->setParameter('{{ entity }}', $constraint->entityClass)
            ->setCode(EntityExistsByField::NOT_EXISTS_ENTITY_BY_FIELD_ERROR)
            ->addViolation();
    }
}