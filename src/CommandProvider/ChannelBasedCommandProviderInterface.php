<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ChannelBasedCommandProviderInterface
{
    public function validate(Request $httpRequest, ChannelInterface $channel): ConstraintViolationListInterface;

    public function getCommand(Request $httpRequest, ChannelInterface $channel): CommandInterface;
}
