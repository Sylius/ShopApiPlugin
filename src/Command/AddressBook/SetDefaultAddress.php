<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\AddressBook;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class SetDefaultAddress implements CommandInterface
{
    /** @var int|string */
    protected $id;

    /** @var string */
    protected $userEmail;

    /**
     * @param int|string $id
     */
    public function __construct($id, string $userEmail)
    {
        $this->id = $id;
        $this->userEmail = $userEmail;
    }

    /**
     * @return int|string $id
     */
    public function id()
    {
        return $this->id;
    }

    public function userEmail(): string
    {
        return $this->userEmail;
    }
}
