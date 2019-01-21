<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\RemoveCoupon;
use Symfony\Component\HttpFoundation\Request;

class RemoveCouponRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    public function populateData(Request $request): void
    {
        $this->token = $request->attributes->get('token');
    }

    public function getCommand(): object
    {
        return new RemoveCoupon($this->token);
    }
}
