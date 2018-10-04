<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\AddCoupon;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;

final class AddCouponRequest implements CommandRequestInterface
{
    /** @var string|null */
    private $token;

    /** @var string|null */
    private $coupon;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->coupon = $request->request->get('coupon');
    }

    public function getCommand(): CommandInterface
    {
        return new AddCoupon($this->token, $this->coupon);
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
