<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

class RemoveAddress
{
    /**
     * @var mixed
     */
    public $id;

    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
}
