<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Model\Address;

final class CreateAddress
{
    /**
     * @var Address
     */
    private $address;

    /**
     * @var string
     */
    private $userEmail;

    /**
     * @param Address $address
     * @param string $userEmail
     */
    public function __construct(Address $address, string $userEmail)
    {
        $this->address = $address;
        $this->userEmail = $userEmail;
    }

    /**
     * @return Address
     */
    public function address(): Address
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function userEmail(): string
    {
        return $this->userEmail;
    }
}
