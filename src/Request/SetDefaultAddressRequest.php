<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\SetDefaultAddress;
use Symfony\Component\HttpFoundation\Request;

class SetDefaultAddressRequest implements UserEmailBasedCommandRequestInterface
{
    /** @var mixed */
    protected $id;

    /** @var string */
    protected $userEmail;

    public function populateData(Request $request): void
    {
        $this->id = $request->attributes->get('id');
    }

    public function setUserEmail(string $userEmail): void
    {
        $this->userEmail = $userEmail;
    }

    public function getCommand(): object
    {
        return new SetDefaultAddress($this->id, $this->userEmail);
    }
}
