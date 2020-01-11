<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class RequestPasswordResettingAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var CommandProviderInterface */
    private $generateResetPasswordTokenCommandProvider;

    /** @var ChannelBasedCommandProviderInterface */
    private $sendResetPasswordTokenCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        ChannelContextInterface $channelContext,
        CommandProviderInterface $generateResetPasswordTokenCommandProvider,
        ChannelBasedCommandProviderInterface $sendResetPasswordTokenCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->channelContext = $channelContext;
        $this->generateResetPasswordTokenCommandProvider = $generateResetPasswordTokenCommandProvider;
        $this->sendResetPasswordTokenCommandProvider = $sendResetPasswordTokenCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $validationResults = $this->generateResetPasswordTokenCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }
        $this->bus->dispatch($this->generateResetPasswordTokenCommandProvider->getCommand($request));
        $this->bus->dispatch($this->sendResetPasswordTokenCommandProvider->getCommand($request, $channel));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
