<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

abstract class JsonApiTestCase extends \Lakion\ApiTestCase\JsonApiTestCase
{
    protected function get($id)
    {
        if (property_exists(static::class, 'container')) {
            return static::$container->get($id);
        }

        return parent::get($id);
    }
}
