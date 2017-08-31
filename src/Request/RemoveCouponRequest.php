<?php

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\RemoveCoupon;
use Symfony\Component\HttpFoundation\Request;

final class RemoveCouponRequest
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    /**
     * @return RemoveCoupon
     */
    public function getCommand()
    {
        return new RemoveCoupon($this->token);
    }
}
