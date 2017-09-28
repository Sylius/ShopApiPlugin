<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PutOptionBasedConfigurableItemToCart;
use Symfony\Component\HttpFoundation\Request;

final class PutOptionBasedConfigurableItemToCartRequest
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
     * @var array|null
     */
    private $options;

    /**
     * @var int
     */
    private $quantity;

    private function __construct($token, $productCode, $options, $quantity)
    {
        $this->token = $token;
        $this->productCode = $productCode;
        $this->options = $options;
        $this->quantity = $quantity;
    }

    public static function fromArray(array $item)
    {
        return new self($item['token'] ?? null, $item['productCode'] ?? null, $item['options'] ?? null, $item['quantity'] ?? null);
    }

    public static function fromRequest(Request $request)
    {
        return new self($request->attributes->get('token'), $request->request->get('productCode'), $request->request->get('options'), $request->request->get('quantity'));
    }

    /**
     * @return PutOptionBasedConfigurableItemToCart
     */
    public function getCommand()
    {
        return new PutOptionBasedConfigurableItemToCart($this->token, $this->productCode, $this->options, $this->quantity);
    }
}
