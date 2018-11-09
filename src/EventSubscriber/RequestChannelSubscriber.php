<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventSubscriber;

use Sylius\ShopApiPlugin\Checker\ChannelExistenceCheckerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestChannelSubscriber implements EventSubscriberInterface
{
    /** @var ChannelExistenceCheckerInterface */
    private $channelExistenceChecker;

    public function __construct(ChannelExistenceCheckerInterface $channelExistenceChecker)
    {
        $this->channelExistenceChecker = $channelExistenceChecker;
    }

    public function checkChannelCode(FilterControllerEvent $event): void
    {
        $requestAttributes = $event->getRequest()->attributes;

        if (!$requestAttributes->has('channelCode')) {
            return;
        }

        $this->channelExistenceChecker->withCode($requestAttributes->get('channelCode'));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'checkChannelCode',
        ];
    }
}
