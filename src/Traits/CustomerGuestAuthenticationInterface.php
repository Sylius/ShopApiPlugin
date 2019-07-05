<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Traits;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface CustomerGuestAuthenticationInterface extends UserInterface
{
    public function getAuthorizedOrder(): OrderInterface;

    public function setAuthorizedOrder(OrderInterface $authorizedOrder): void;
}
