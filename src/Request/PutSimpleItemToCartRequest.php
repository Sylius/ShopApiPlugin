<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Request;

final class PutSimpleItemToCartRequest implements CommandRequestInterface
{
    /** @var string */
    private $token;

    /** @var string */
    private $productCode;

    /** @var int */
    private $quantity;

    private function __construct($token, $productCode, $quantity)
    {
        $this->token = $token;
        $this->productCode = $productCode;
        $this->quantity = $quantity;
    }

    public static function fromArray(array $item): self
    {
        return new self($item['token'] ?? null, $item['productCode'] ?? null, $item['quantity'] ?? null);
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->attributes->get('token'), $request->request->get('productCode'), $request->request->getInt('quantity', 1));
    }

    public function getCommand(): CommandInterface
    {
        return new PutSimpleItemToCart($this->token, $this->productCode, $this->quantity);
    }
}
