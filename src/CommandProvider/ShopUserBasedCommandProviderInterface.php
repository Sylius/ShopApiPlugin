<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ShopUserBasedCommandProviderInterface
{
    public function validate(
        Request $httpRequest,
        ShopUserInterface $user,
        array $constraints = null,
        array $groups = null
    ): ConstraintViolationListInterface;

    public function getCommand(Request $httpRequest, ShopUserInterface $user): CommandInterface;
}
