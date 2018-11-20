<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Http;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Checker\ChannelExistenceCheckerInterface;
use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class RequestBasedLocaleProvider implements RequestBasedLocaleProviderInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ChannelExistenceCheckerInterface */
    private $channelExistenceChecker;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ChannelExistenceCheckerInterface $channelExistenceChecker,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->channelRepository = $channelRepository;
        $this->channelExistenceChecker = $channelExistenceChecker;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    /** @throws ChannelNotFoundException|\InvalidArgumentException */
    public function getLocaleCode(Request $request): string
    {
        Assert::true($request->attributes->has('channelCode'));

        $channelCode = $request->attributes->get('channelCode');
        $this->channelExistenceChecker->withCode($channelCode);

        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        return $this->supportedLocaleProvider->provide($request->query->get('locale'), $channel);
    }
}
