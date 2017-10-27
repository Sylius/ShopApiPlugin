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

    /**
     * @var ShopUserInterface
     */
    private $user;

    public function __construct(Request $request, ShopUserInterface $user)
    {
        $this->id = $request->request->get('id');
        $this->user = $user;
    }

    /**
     * @return SetDefaultAddress
     */
    public function getCommand()
    {
        return new SetDefaultAddress($this->id, $this->user);
    }
}
