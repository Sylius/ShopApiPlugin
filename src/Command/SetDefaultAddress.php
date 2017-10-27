<?php

namespace Sylius\ShopApiPlugin\Command;

use Sylius\Component\Core\Model\ShopUserInterface;

class SetDefaultAddress
{
    /**
     * @var mixed
     */
    public $id;

    /**
     * @var ShopUserInterface
     */
    public $user;

    public function __construct($id, ShopUserInterface $user)
    {
        $this->id = $id;
        $this->user = $user;
    }
}
