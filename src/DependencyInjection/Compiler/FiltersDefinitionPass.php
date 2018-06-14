<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Gets every filter definition and adds them to the Filter Extension.
 *
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
class FiltersDefinitionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sylius.shop_api_plugin.filters.filter_extension')) {
            return;
        }

        $definition = $container->findDefinition('sylius.shop_api_plugin.filters.filter_extension');
        $taggedServices = $container->findTaggedServiceIds('sylius.shop_api_plugin.filter');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addFilter', [new Reference($id)]);
        }
    }
}
