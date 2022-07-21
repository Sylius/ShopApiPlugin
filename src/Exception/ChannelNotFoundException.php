<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Exception;

final class ChannelNotFoundException extends \InvalidArgumentException
{
    public static function occur(): self
    {
        return new self('Channel has not been found.');
    }

    public static function withCode(string $channelCode): self
    {
        return new self(sprintf('Channel with code %s has not been found.', $channelCode));
    }
}
