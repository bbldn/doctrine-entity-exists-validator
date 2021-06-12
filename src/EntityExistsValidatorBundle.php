<?php

namespace BBLDN\EntityExistsValidatorBundle;

use BBLDN\EntityExistsValidatorBundle\DependencyInjection\Extension\EntityExistsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EntityExistsValidatorBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerExtension(new EntityExistsExtension());
    }
}