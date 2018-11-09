<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Checker;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            throw new NotFoundHttpException(sprintf('Channel with code %s has not been found.', $channelCode));
        }
    }
}
