<?php

namespace BBLDN\EntityExistsValidatorBundle\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use BBLDN\EntityExistsValidatorBundle\DependencyInjection\Helper\Context;
use BBLDN\EntityExistsValidatorBundle\Doctrine\Validator\EntityExistsValidator;
use BBLDN\EntityExistsValidatorBundle\Doctrine\Validator\EntityExistsByFieldValidator;

class EntityExistsExtension implements ExtensionInterface
{
    private Context $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return bool
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     */
    public function getNamespace(): bool
    {
        return false;
    }

    /**
     * @return false
     */
    public function getXsdValidationBasePath(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->context->getExtensionTag();
    }

    /**
     * @param ContainerBuilder $container
     * @return void
     */
    private function definitionEntityExistsValidator(ContainerBuilder $container): void
    {
        $definition = new Definition();
        $definition->setLazy(true);
        $definition->setClass(EntityExistsValidator::class);
        $definition->addTag($this->context->getValidatorConstraintValidatorTag());

        $definition->setArgument(0, new Reference('doctrine'));

        $container->setDefinition(EntityExistsValidator::class, $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @return void
     */
    private function definitionEntityExistsByFieldValidator(ContainerBuilder $container): void
    {
        $definition = new Definition();
        $definition->setLazy(true);
        $definition->setClass(EntityExistsByFieldValidator::class);
        $definition->addTag($this->context->getValidatorConstraintValidatorTag());

        $definition->setArgument(0, new Reference('doctrine'));

        $container->setDefinition(EntityExistsByFieldValidator::class, $definition);
    }

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->definitionEntityExistsValidator($container);
        $this->definitionEntityExistsByFieldValidator($container);
    }
}