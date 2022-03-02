<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
