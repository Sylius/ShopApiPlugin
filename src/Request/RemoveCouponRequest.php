<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\Cart\RemoveCoupon;
use Symfony\Component\HttpFoundation\Request;

class RemoveCouponRequest
{
    /** @var string */
    protected $token;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    public function getCommand(): RemoveCoupon
    {
        return new RemoveCoupon($this->token);
    }
}
