<?php

declare(strict_types=1);

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
    public function __construct($orderToken, $product, $productVariant, $quantity)
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
    public function orderToken()
    {
        return $this->orderToken;
    }

    /**
     * @return string
     */
    public function product()
    {
        return $this->product;
    }

    /**
     * @return string
     */
    public function productVariant()
    {
        return $this->productVariant;
    }

    /**
     * @return int
     */
    public function quantity()
    {
        return $this->quantity;
    }
}
