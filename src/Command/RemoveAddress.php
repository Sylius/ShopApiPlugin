<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class RemoveAddress implements CommandInterface
{
    /** @var mixed */
    private $id;

    /** @var string */
    private $userEmail;

    public function __construct($id, string $userEmail)
    {
        $this->id = $id;
        $this->userEmail = $userEmail;
    }

    public function id()
    {
        return $this->id;
    }

    public function userEmail(): string
    {
        return $this->userEmail;
    }
}
