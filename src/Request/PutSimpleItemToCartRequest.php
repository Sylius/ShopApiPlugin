<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
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
    private $product;

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
        $this->quantity = $request->request->getInt('quantity');
    }

    /**
     * @return PutSimpleItemToCart
     */
    public function getCommand(): \Sylius\ShopApiPlugin\Command\PutSimpleItemToCart
    {
        return new PutSimpleItemToCart($this->token, $this->product, $this->quantity);
    }
}
