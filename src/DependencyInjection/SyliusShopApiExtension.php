<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusShopApiExtension extends Extension
{
    /** @inheritdoc */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        foreach ($config['view_classes'] as $view => $class) {
            $container->setParameter(sprintf('sylius.shop_api.view.%s.class', $view), $class);
        }

        foreach ($config['request_classes'] as $request => $class) {
            $container->setParameter(sprintf('sylius.shop_api.request.%s.class', $request), $class);
        }

        $container->setParameter('sylius.shop_api.included_attributes', $config['included_attributes']);

        $loader->load('services.xml');
    }
}
