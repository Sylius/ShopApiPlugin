<?php

namespace Sylius\ShopApiPlugin\Model;

use Webmozart\Assert\Assert;

final class PaginatorDetails
{
    /** @var string */
    private $route;

    /** @var int */
    private $limit;

    /** @var int */
    private $page;

    /** @var array */
    private $getParameters;

    public function __construct(string $route, array $getParameters)
    {
        $this->route = $route;
        $this->limit = $getParameters['limit'] ?? 10;
        $this->page = $getParameters['page'] ?? 1;
        $this->getParameters = $getParameters;
    }

    public function route(): string
    {
        return $this->route;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function getParameters(): array
    {
        return $this->getParameters;
    }

    public function addParameter(string $key, string $value)
    {
        Assert::keyNotExists($this->getParameters, $key);

        $this->getParameters[$key] = $value;
    }
}
