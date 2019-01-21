<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\RemoveAddress;
use Symfony\Component\HttpFoundation\Request;

class RemoveAddressRequest implements UserEmailBasedCommandRequestInterface
{
    /** @var int|string */
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
        return new RemoveAddress($this->id, $this->userEmail);
    }
}
