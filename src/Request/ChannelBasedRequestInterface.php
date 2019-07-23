<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;

interface ChannelBasedRequestInterface
{
    public static function fromRequestAndChannel(Request $request, ChannelInterface $channel): self;

    public function getCommand(): CommandInterface;
}
