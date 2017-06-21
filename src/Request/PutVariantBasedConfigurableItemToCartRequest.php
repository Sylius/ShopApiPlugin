<?php

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Symfony\Component\HttpFoundation\Request;

final class PutVariantBasedConfigurableItemToCartRequest
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
     * @var string
     */
    private $variantCode;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->productCode = $request->request->get('productCode');
        $this->variantCode = $request->request->get('variantCode');
        $this->quantity = $request->request->get('quantity');
    }

    /**
     * @return PutVariantBasedConfigurableItemToCart
     */
    public function getCommand()
    {
        return new PutVariantBasedConfigurableItemToCart($this->token, $this->productCode, $this->variantCode, $this->quantity);
    }
}
