<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Request;

use Sylius\SyliusShopApiPlugin\Command\RemoveCoupon;
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
