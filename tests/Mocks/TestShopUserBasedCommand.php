<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Mocks;

use Sylius\ShopApiPlugin\Command\CommandInterface;

final class TestShopUserBasedCommand implements CommandInterface
{
    /** @var string */
    private $token;

    /** @var string */
    private $email;

    public function __construct(string $token, string $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function email(): string
    {
        return $this->email;
    }
}
