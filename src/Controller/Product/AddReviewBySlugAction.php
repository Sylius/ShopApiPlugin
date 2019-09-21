<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class AddReviewBySlugAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var ChannelBasedCommandProviderInterface */
    private $addReviewBySlugCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        ChannelContextInterface $channelContext,
        ChannelBasedCommandProviderInterface $addReviewBySlugCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->channelContext = $channelContext;
        $this->addReviewBySlugCommandProvider = $addReviewBySlugCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $validationResults = $this->addReviewBySlugCommandProvider->validate($request, $channel);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        $this->bus->dispatch($this->addReviewBySlugCommandProvider->getCommand($request, $channel));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }
}
