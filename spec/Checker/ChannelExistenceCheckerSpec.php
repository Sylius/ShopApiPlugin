<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Checker\ChannelExistenceCheckerInterface;
use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;

final class ChannelExistenceCheckerSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository);
    }

    function it_implements_channel_existence_checker_interface(): void
    {
        $this->shouldImplement(ChannelExistenceCheckerInterface::class);
    }

    function it_does_nothing_if_channel_with_given_code_exists(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
    ): void {
        $channelRepository->findOneByCode('WEB_US')->willReturn($channel);

        $this->withCode('WEB_US');
    }

    function it_throws_exception_if_channel_with_given_code_does_not_exist(
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $channelRepository->findOneByCode('WEB_US')->willReturn(null);

        $this
            ->shouldThrow(ChannelNotFoundException::class)
            ->during('withCode', ['WEB_US'])
        ;
    }
}
