<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\AddressBook\SetDefaultAddress;
use Symfony\Component\HttpFoundation\Request;

class SetDefaultAddressRequest
{
    /** @var mixed */
    protected $id;

    /** @var string */
    protected $userEmail;

    public function __construct(Request $request, string $userEmail)
    {
        $this->id = $request->attributes->get('id');
        $this->userEmail = $userEmail;
    }

    public function getCommand(): SetDefaultAddress
    {
        return new SetDefaultAddress($this->id, $this->userEmail);
    }
}
