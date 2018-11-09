<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Checker;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

interface ChannelExistenceCheckerInterface
{
    /** @throws NotFoundHttpException if channel with passed code does not exits */
    public function withCode(string $channelCode): void;
}
