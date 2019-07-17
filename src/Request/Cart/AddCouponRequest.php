<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Sylius\ShopApiPlugin\Command\Cart\AddCoupon;
use Symfony\Component\HttpFoundation\Request;

class AddCouponRequest
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $coupon;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->coupon = $request->request->get('coupon');
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getCoupon(): ?string
    {
        return $this->coupon;
    }
}
