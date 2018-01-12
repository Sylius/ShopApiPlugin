<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Sylius\ShopApiPlugin\EventListener\InteractiveLoginListener;

final class ShopApiExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        foreach ($config['view_classes'] as $view => $class) {
            $container->setParameter(sprintf('sylius.shop_api.view.%s.class', $view), $class);
        }

        $container->setParameter('sylius.shop_api.included_attributes', $config['included_attributes']);

        if ($config['auto_pickup_cart']) {
            $this->registerInteractiveLoginListener($container);
        }

        $loader->load('services.xml');
    }

    private function registerInteractiveLoginListener(ContainerBuilder $container)
    {
        $interactiveLoginListener = new Definition(InteractiveLoginListener::class, [
            new Reference('sylius.manager.order'),
            new Reference('sylius.context.cart'),
        ]);

        $interactiveLoginListener->addTag('kernel.event_listener', [
            'event'  => 'security.interactive_login',
            'method' => 'onInteractiveLogin',
        ]);

        $container->addDefinitions([
            'sylius.shop_api_plugin.event_listener.interactive_login_listener' => $interactiveLoginListener,
        ]);
    }
}
