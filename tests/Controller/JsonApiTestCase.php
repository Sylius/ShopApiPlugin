<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

abstract class JsonApiTestCase extends \ApiTestCase\JsonApiTestCase
{
    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->dataFixturesPath = __DIR__ . '/../DataFixtures/ORM';
        $this->expectedResponsesPath = __DIR__ . '/../Responses/Expected';
    }

    protected function get($id)
    {
        if (property_exists(static::class, 'container')) {
            return static::$container->get($id);
        }

        return parent::get($id);
    }
}
