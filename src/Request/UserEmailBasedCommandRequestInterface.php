<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

interface UserEmailBasedCommandRequestInterface extends CommandRequestInterface
{
    public function setUserEmail(string $userEmail): void;
}
