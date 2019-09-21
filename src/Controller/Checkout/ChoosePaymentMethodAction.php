<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class ChoosePaymentMethodAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var CommandProviderInterface */
    private $choosePaymentMethodCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        CommandProviderInterface $choosePaymentMethodCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->choosePaymentMethodCommandProvider = $choosePaymentMethodCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        $this->bus->dispatch($this->choosePaymentMethodCommandProvider->getCommand($request));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
