<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusShopApiExtension extends Extension
{

    /** {@inheritdoc} */
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

        $container->setParameter('sylius.shop_api.disable_liip_image', $config['parameters']['disable_liip_image']);
        $container->setParameter('sylius.shop_api.cloud_url', $config['parameters']['cloud_url']);

        $loader->load('services.xml');
    }
}
