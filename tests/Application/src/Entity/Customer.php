<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Application\src\Entity;


use Sylius\ShopApiPlugin\Traits\CustomerGuestAuthenticationInterface;
use Sylius\ShopApiPlugin\Traits\CustomerGuestAuthenticationTrait;
use Sylius\Component\Core\Model\Customer as BaseCustomer;

final class Customer extends BaseCustomer implements CustomerGuestAuthenticationInterface
{
    use CustomerGuestAuthenticationTrait;
}
