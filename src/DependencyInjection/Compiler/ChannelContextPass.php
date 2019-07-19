<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ChannelContextPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // TODO: Remove after next Sylius release (1.6.0)
        $container->removeDefinition('sylius.listener.user_impersonated');

        $container->removeDefinition('sylius.context.channel.cached');
    }
}
