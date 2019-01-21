<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Exception;

final class CannotParseCommand extends \InvalidArgumentException
{
    public static function withCommandName(string $commandName): self
    {
        return new self(sprintf('Cannot parse request for command with name "%s".', $commandName));
    }
}
