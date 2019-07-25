<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Sylius\ShopApiPlugin\Command\Cart\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class PutVariantBasedConfigurableItemToCartRequest implements RequestInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $productCode;

    /** @var string */
    protected $variantCode;

    /** @var int */
    protected $quantity;

    protected function __construct($token, $productCode, $variantCode, $quantity)
    {
        $this->token = $token;
        $this->productCode = $productCode;
        $this->variantCode = $variantCode;
        $this->quantity = $quantity;
    }

    public static function fromArray(array $item): self
    {
        return new self(
            $item['token'] ?? null,
            $item['productCode'] ?? null,
            $item['variantCode'] ?? null,
            $item['quantity'] ?? null
        );
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self(
            $request->attributes->get('token'),
            $request->request->get('productCode'),
            $request->request->get('variantCode'),
            $request->request->getInt('quantity', 1)
        );
    }

    public function getCommand(): CommandInterface
    {
        return new PutVariantBasedConfigurableItemToCart(
            $this->token,
            $this->productCode,
            $this->variantCode,
            $this->quantity
        );
    }
}
