<?php

namespace spec\Sylius\ShopApiPlugin\Validator;

use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Validator\ChannelWithGivenCodeDoesNotExistsValidator;
use Sylius\ShopApiPlugin\Validator\Constraints\ChannelWithGivenCodeDoesNotExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChannelWithGivenCodeDoesNotExistsValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, ChannelRepositoryInterface $channelRepository)
    {
        $this->beConstructedWith($channelRepository);

        $this->initialize($executionContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelWithGivenCodeDoesNotExistsValidator::class);
    }

    function it_does_not_add_constraint_if_channel_exists(
        ChannelInterface $channel,
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $executionContext
    ) {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('WEB_GB', new ChannelWithGivenCodeDoesNotExists());
    }

    function it_adds_constraint_if_channel_does_not_exits_exists(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $executionContext
    ) {
        $channelRepository->findOneByCode('WEB_GB')->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.channel.not_exists')->shouldBeCalled();

        $this->validate('WEB_GB', new ChannelWithGivenCodeDoesNotExists());
    }
}
