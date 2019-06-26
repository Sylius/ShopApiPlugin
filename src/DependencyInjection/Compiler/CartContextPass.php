<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CartContextPass implements CompilerPassInterface
{
    public const CART_CONTEXT_SERVICE_TAG = 'sylius.context.cart';
    private const COMPOSITE_ID = 'sylius.shop_api_plugin.context.cart';
    private const EXCLUDED_SERVICE = 'sylius.context.cart.new';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::COMPOSITE_ID)) {
            return;
        }

        $contextDefinition = $container->findDefinition(self::COMPOSITE_ID);

        $taggedServices = $container->findTaggedServiceIds(self::CART_CONTEXT_SERVICE_TAG);
        foreach ($taggedServices as $id => $tags) {
            $this->addMethodCalls($contextDefinition, $id, $tags);
        }
    }

    private function addMethodCalls(Definition $contextDefinition, string $id, array $tags): void
    {
        if ($id === self::EXCLUDED_SERVICE) {
            return;
        }

        foreach ($tags as $attributes) {
            $contextDefinition->addMethodCall('addContext', [new Reference($id), $attributes['priority'] ?? 0]);
        }
    }
}
