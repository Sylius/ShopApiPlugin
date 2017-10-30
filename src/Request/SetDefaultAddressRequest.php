<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\SetDefaultAddress;
use Symfony\Component\HttpFoundation\Request;

final class SetDefaultAddressRequest
{
    /**
     * @var mixed
     */
    private $id;

    public function __construct(Request $request)
    {
        $this->id = $request->attributes->get('id');
    }

    /**
     * @return SetDefaultAddress
     */
    public function getCommand()
    {
        return new SetDefaultAddress($this->id);
    }
}
