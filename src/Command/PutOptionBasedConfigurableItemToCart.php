<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class PutOptionBasedConfigurableItemToCart
{
    /**
     * @var string
     */
    private $orderToken;

    /**
     * @var string
     */
    private $product;

    /**
     * @var array
     */
    private $options;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param string $orderToken
     * @param string $product
     * @param array $options
     * @param int $quantity
     */
    public function __construct(string $orderToken, string $product, array $options, int $quantity)
    {
        Assert::notEmpty($options, 'Options array cannot be empty');
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->product = $product;
        $this->options = $options;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return string
     */
    public function product(): string
    {
        return $this->product;
    }

    /**
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * @return int
     */
    public function quantity(): int
    {
        return $this->quantity;
    }
}
