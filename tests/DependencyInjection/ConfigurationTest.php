<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\ShopApiPlugin\DependencyInjection\Configuration;

final class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_processed_attribute_codes_which_should_be_serialized()
    {
        $this->assertProcessedConfigurationEquals([
            'shop_api_plugin' => [
                'included_attributes' => [
                    'FIRST_CODE',
                    'SECOND_CODE',
                ],
            ],
        ], [
            'included_attributes' => [
                'FIRST_CODE',
                'SECOND_CODE',
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_processed_empty_attribute_codes_list_and_returns_empty_array()
    {
        $this->assertProcessedConfigurationEquals([], ['included_attributes' => []]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
