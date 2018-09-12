<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Symfony\Component\HttpFoundation\Request;

final class PutVariantBasedConfigurableItemToCartRequest
{
    /** @var string */
    private $token;

    /** @var string */
    private $productCode;

    /** @var string */
    private $variantCode;

    /** @var int */
    private $quantity;

    private function __construct($token, $productCode, $variantCode, $quantity)
    {
        $this->token = $token;
        $this->productCode = $productCode;
        $this->variantCode = $variantCode;
        $this->quantity = $quantity;
    }

    public static function fromArray(array $item): self
    {
        return new self($item['token'] ?? null, $item['productCode'] ?? null, $item['variantCode'] ?? null, $item['quantity'] ?? null);
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->attributes->get('token'), $request->request->get('productCode'), $request->request->get('variantCode'), $request->request->getInt('quantity', 1));
    }

    public function getCommand(): PutVariantBasedConfigurableItemToCart
    {
        return new PutVariantBasedConfigurableItemToCart($this->token, $this->productCode, $this->variantCode, $this->quantity);
    }
}
