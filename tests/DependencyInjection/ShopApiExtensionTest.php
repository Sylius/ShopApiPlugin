<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\ShopApiPlugin\DependencyInjection\ShopApiExtension;

final class ShopApiExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_sets_up_parameter_with_attributes_to_serialize()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('sylius.shop_api.included_attributes', ['ATTRIBUTE_CODE']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [new ShopApiExtension()];
    }

    /**
     * {@inheritdoc}
     */
    protected function getMinimalConfiguration()
    {
        return [
            'included_attributes' => [
                'ATTRIBUTE_CODE',
            ],
        ];
    }
}
