<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Http;

use Sylius\ShopApiPlugin\Checker\ChannelExistenceCheckerInterface;
use Sylius\ShopApiPlugin\Exception\ChannelNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestChannelEnsurer implements EventSubscriberInterface
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

        try {
            $this->channelExistenceChecker->withCode($requestAttributes->get('channelCode'));
        } catch (ChannelNotFoundException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'checkChannelCode',
        ];
    }
}
