<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class PutSimpleItemToCart
{
    /** @var string */
    private $orderToken;

    /** @var string */
    private $product;

    /** @var int */
    private $quantity;

    /**
     * @param string $orderToken
     * @param string $product
     * @param int $quantity
     */
    public function __construct($orderToken, $product, $quantity)
    {
        Assert::string($orderToken, 'Expected order token to be string, got %s');
        Assert::string($product, 'Expected product code to be string, got %s');
        Assert::integer($quantity, 'Expected quantity to be integer, got %s');
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function orderToken()
    {
        return $this->orderToken;
    }

    public function product()
    {
        return $this->product;
    }

    public function quantity()
    {
        return $this->quantity;
    }
}
