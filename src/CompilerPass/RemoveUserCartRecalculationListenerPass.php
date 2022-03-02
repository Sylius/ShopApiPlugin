<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
