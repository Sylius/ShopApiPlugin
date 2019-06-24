<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CartContextPass implements CompilerPassInterface
{
    public const CART_CONTEXT_SERVICE_TAG = 'sylius.context.cart';

    private $compositeId = 'sylius.shop_api_plugin.context.cart';
    private $tagName = self::CART_CONTEXT_SERVICE_TAG;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has($this->compositeId)) {
            return;
        }

        $contextDefinition = $container->findDefinition($this->compositeId);

        $taggedServices = $container->findTaggedServiceIds($this->tagName);
        foreach ($taggedServices as $id => $tags) {
            $this->addMethodCalls($contextDefinition, $id, $tags);
        }
    }

    private function addMethodCalls(Definition $contextDefinition, string $id, array $tags): void
    {
        if ($id === 'sylius.context.cart.new') {
            return;
        }

        foreach ($tags as $attributes) {
            $contextDefinition->addMethodCall('addContext', [new Reference($id), $attributes['priority'] ?? 0]);
        }
    }
}
