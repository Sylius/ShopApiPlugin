<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Sylius\Component\Core\Model\ShopUserInterface;

class SetDefaultAddress
{
    /**
     * @var mixed
     */
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}
