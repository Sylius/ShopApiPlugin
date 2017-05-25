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
    private $product;

    /**
     * @var string
     */
    private $variant;

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
        $this->product = $request->request->get('productCode');
        $this->variant = $request->request->get('variantCode');
        $this->quantity = $request->request->getInt('quantity');
    }

    /**
     * @return PutVariantBasedConfigurableItemToCart
     */
    public function getCommand()
    {
        return new PutVariantBasedConfigurableItemToCart($this->token, $this->product, $this->variant, $this->quantity);
    }
}
