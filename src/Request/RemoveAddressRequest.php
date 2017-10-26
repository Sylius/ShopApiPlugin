<?php

namespace Sylius\ShopApiPlugin\Request;

use Sylius\Component\User\Model\UserInterface;
use Sylius\ShopApiPlugin\Command\RemoveAddress;
use Symfony\Component\HttpFoundation\Request;

class RemoveAddressRequest
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var UserInterface
     */
    private $user;

    public function __construct(Request $request, UserInterface $user)
    {
        $this->id = $request->attributes->get('id');
        $this->user = $user;
    }

    public function getCommand()
    {
        return new RemoveAddress($this->id, $this->user);
    }
}