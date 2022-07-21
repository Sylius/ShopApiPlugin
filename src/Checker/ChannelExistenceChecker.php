<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Checker;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;

final class ChannelExistenceChecker implements ChannelExistenceCheckerInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function withCode(string $channelCode): void
    {
        $channel = $this->channelRepository->findOneByCode($channelCode);

        if (null === $channel) {
            throw ChannelNotFoundException::withCode($channelCode);
        }
    }
}
