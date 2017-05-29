<?php

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class PutVariantBasedConfigurableItemToCart
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
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $productVariant;

    /**
     * @param string $orderToken
     * @param string $product
     * @param string $productVariant
     * @param int $quantity
     */
    public function __construct(string $orderToken, string $product, string $productVariant, int $quantity)
    {
        Assert::string($orderToken, 'Expected order token to be string, got %s');
        Assert::string($product, 'Expected product code to be string, got %s');
        Assert::string($productVariant, 'Expected product variant code to be string, got %s');
        Assert::integer($quantity, 'Expected quantity to be integer, got %s');
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->productVariant = $productVariant;
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
     * @return string
     */
    public function productVariant(): string
    {
        return $this->productVariant;
    }

    /**
     * @return int
     */
    public function quantity(): int
    {
        return $this->quantity;
    }
}
