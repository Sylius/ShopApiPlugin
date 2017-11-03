<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

class SetDefaultAddress
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }
}
