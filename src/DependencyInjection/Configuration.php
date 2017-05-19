<?php

namespace Sylius\ShopApiPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('shop_api');
        $rootNode
            ->children()
                ->arrayNode('included_attributes')
                    ->prototype('scalar');

        return $treeBuilder;
    }
}
