<?php

namespace BBLDN\EntityExistsValidatorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use BBLDN\EntityExistsValidatorBundle\DependencyInjection\Helper\Context;
use BBLDN\EntityExistsValidatorBundle\DependencyInjection\Extension\EntityExistsExtension;

class EntityExistsValidatorBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $context = new Context();
        $container->registerExtension(new EntityExistsExtension($context));
    }
}