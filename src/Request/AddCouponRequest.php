<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\AddCoupon;
use Symfony\Component\HttpFoundation\Request;

final class AddCouponRequest
{
    /** @var string */
    private $token;

    /** @var string */
    private $coupon;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->coupon = $request->request->get('coupon');
    }

    public function getCommand()
    {
        return new AddCoupon($this->token, $this->coupon);
    }
}
