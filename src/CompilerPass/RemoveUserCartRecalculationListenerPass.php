<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveUserCartRecalculationListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /*
         * Remove the core user cart recalculation listener because it results in deadlocks.
         * See https://github.com/Sylius/ShopApiPlugin/issues/681
         */
        $container->removeDefinition('sylius.listener.user_cart_recalculation');
    }
}
