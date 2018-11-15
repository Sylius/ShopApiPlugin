<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Exception;

final class ChannelNotFoundException extends \InvalidArgumentException
{
    public static function withCode(string $channelCode): self
    {
        return new self(sprintf('Channel with code %s has not been found.', $channelCode));
    }
}
