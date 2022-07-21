<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Checker;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

interface ChannelExistenceCheckerInterface
{
    /** @throws NotFoundHttpException if channel with passed code does not exits */
    public function withCode(string $channelCode): void;
}
