<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Sylius\Component\Core\Model\ShopUserInterface;

class RemoveAddress
{
    /**
     * @var mixed
     */
    public $id;

    /**
     * @var ShopUserInterface
     */
    public $user;

    /**
     * @param $id
     * @param ShopUserInterface $user
     */
    public function __construct($id, ShopUserInterface $user)
    {
        $this->id = $id;
        $this->user = $user;
    }
}
