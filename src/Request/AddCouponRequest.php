<?php

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\AddCoupon;
use Symfony\Component\HttpFoundation\Request;

final class AddCouponRequest
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $coupon;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->coupon = $request->request->get('coupon');
    }

    /**
     * @return AddCoupon
     */
    public function getCommand(): \Sylius\ShopApiPlugin\Command\AddCoupon
    {
        return new AddCoupon($this->token, $this->coupon);
    }
}
