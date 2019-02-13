<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

abstract class JsonApiTestCase extends \Lakion\ApiTestCase\JsonApiTestCase
{

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->dataFixturesPath = __DIR__ . '/../DataFixtures/ORM';
    }

    protected function get($id)
    {
        if (property_exists(static::class, 'container')) {
            return static::$container->get($id);
        }

        return parent::get($id);
    }
}
