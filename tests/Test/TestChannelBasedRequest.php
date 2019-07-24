<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Test;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class TestChannelBasedRequest implements ChannelBasedRequestInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $channelCode;

    public function __construct(Request $request, string $channelCode)
    {
        $this->token = $request->attributes->get('token');
        $this->channelCode = $channelCode;
    }

    public static function fromHttpRequestAndChannel(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        return new self($request, $channel->getCode());
    }

    public function getCommand(): CommandInterface
    {
        return new TestChannelBasedCommand($this->token, $this->channelCode);
    }
}
