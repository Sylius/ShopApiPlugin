<?php

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class PutVariantBasedConfigurableItemToCart
{
    /**
     * @var string
     */
    private $token;

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
     * @param string $token
     * @param string $product
     * @param string $productVariant
     * @param int $quantity
     */
    public function __construct($token, $product, $productVariant, $quantity)
    {
        Assert::allString([$token, $product, $productVariant]);
        Assert::integer($quantity);
        Assert::greaterThan($quantity, 0);

        $this->token = $token;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->productVariant = $productVariant;
    }

    /**
     * @return string
     */
    public function token()
    {
        return $this->token;
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
