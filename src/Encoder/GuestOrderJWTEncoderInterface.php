<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Encoder;

use Sylius\Component\Core\Model\OrderInterface;

interface GuestOrderJWTEncoderInterface
{
    public function encode(OrderInterface $order): string;

    public function decode(string $jwt): OrderInterface;
}
