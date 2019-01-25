<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ShopApiRequestCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $requestsReferences = [];

        foreach ($container->findTaggedServiceIds('sylius_shop_api.request') as $id => $tags) {
            foreach ($tags as $tag) {
                $requestsReferences[$tag['command']] = new Reference($id);
            }
        }

        $serviceLocator = $container->getDefinition('sylius_shop_api.command_request_locator');
        $serviceLocator->setArguments([$requestsReferences]);
    }
}
