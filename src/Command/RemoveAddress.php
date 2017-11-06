<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

class RemoveAddress
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var string
     */
    private $userEmail;

    /**
     * @param $id
     * @param string $userEmail
     */
    public function __construct($id, string $userEmail)
    {
        $this->id = $id;
        $this->userEmail = $userEmail;
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function userEmail()
    {
        return $this->userEmail;
    }
}
