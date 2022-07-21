<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\ChannelExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChannelExistsValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_channel_exists(
        ChannelInterface $channel,
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('WEB_GB', new ChannelExists());
    }

    function it_adds_constraint_if_channel_does_not_exits_exists(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $channelRepository->findOneByCode('WEB_GB')->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.channel.not_exists')->shouldBeCalled();

        $this->validate('WEB_GB', new ChannelExists());
    }
}
