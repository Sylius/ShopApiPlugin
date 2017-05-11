<?php

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class PutSimpleItemToCart
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
     * @param string $token
     * @param string $product
     * @param int $quantity
     */
    public function __construct($token, $product, $quantity)
    {
        Assert::allString([$token, $product]);
        Assert::integer($quantity);
        Assert::greaterThan($quantity, 0);

        $this->token = $token;
        $this->product = $product;
        $this->quantity = $quantity;
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
     * @return int
     */
    public function quantity()
    {
        return $this->quantity;
    }
}
