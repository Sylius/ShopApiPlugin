<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Model\Address;

final class CreateAddress implements Command
{
    /** @var Address */
    private $address;

    /** @var string */
    private $userEmail;

    public function __construct(Address $address, string $userEmail)
    {
        $this->address = $address;
        $this->userEmail = $userEmail;
    }

    public function address(): Address
    {
        return $this->address;
    }

    public function userEmail(): string
    {
        return $this->userEmail;
    }
}
