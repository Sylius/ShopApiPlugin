<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\RemoveCoupon;
use Symfony\Component\HttpFoundation\Request;

final class RemoveCouponRequest
{
    /** @var string */
    private $token;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    public function getCommand()
    {
        return new RemoveCoupon($this->token);
    }
}
