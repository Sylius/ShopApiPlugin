<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Request;

use Sylius\SyliusShopApiPlugin\Command\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Request;

final class PutSimpleItemToCartRequest
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $productCode;

    /**
     * @var int
     */
    private $quantity;

    private function __construct($token, $productCode, $quantity)
    {
        $this->token = $token;
        $this->productCode = $productCode;
        $this->quantity = $quantity;
    }

    public static function fromArray(array $item)
    {
        return new self($item['token'] ?? null, $item['productCode'] ?? null, $item['quantity'] ?? null);
    }

    public static function fromRequest(Request $request)
    {
        return new self($request->attributes->get('token'), $request->request->get('productCode'), $request->request->getInt('quantity', 1));
    }

    /**
     * @return PutSimpleItemToCart
     */
    public function getCommand()
    {
        return new PutSimpleItemToCart($this->token, $this->productCode, $this->quantity);
    }
}
