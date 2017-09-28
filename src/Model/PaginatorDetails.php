<?php

declare(strict_types=1);

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
    private $parameters;

    public function __construct(string $route, array $parameters)
    {
        $this->route = $route;
        $this->limit = (int) ($parameters['limit'] ?? 10);
        $this->page = (int) ($parameters['page'] ?? 1);
        $this->parameters = $parameters;
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

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function addToParameters(string $key, $value): void
    {
        Assert::keyNotExists($this->parameters, $key);

        $this->parameters[$key] = $value;
    }
}
