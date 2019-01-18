<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PutOptionBasedConfigurableItemToCart;
use Symfony\Component\HttpFoundation\Request;

class PutOptionBasedConfigurableItemToCartRequest
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $productCode;

    /** @var array|null */
    protected $options;

    /** @var int */
    protected $quantity;

    private function __construct($token, $productCode, $options, $quantity)
    {
        $this->token = $token;
        $this->productCode = $productCode;
        $this->options = $options;
        $this->quantity = $quantity;
    }

    public static function fromArray(array $item): self
    {
        return new self($item['token'] ?? null, $item['productCode'] ?? null, $item['options'] ?? null, $item['quantity'] ?? null);
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->attributes->get('token'), $request->request->get('productCode'), $request->request->get('options'), $request->request->getInt('quantity', 1));
    }

    public function getCommand(): object
    {
        return new PutOptionBasedConfigurableItemToCart($this->token, $this->productCode, $this->options, $this->quantity);
    }
}
