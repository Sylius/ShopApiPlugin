<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Exception;

final class UserNotFoundException extends \InvalidArgumentException
{
    public static function occur(): self
    {
        return new self('User with given email does not exist!');
    }

    public static function withEmail(string $email): self
    {
        return new self(sprintf('User with email %s has not been found.', $email));
    }
}
