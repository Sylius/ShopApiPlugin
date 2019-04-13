<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Command\Customer\GenerateResetPasswordToken;
use Sylius\ShopApiPlugin\Command\Customer\SendResetPasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class RequestPasswordResettingAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    public function __construct(ViewHandlerInterface $viewHandler, MessageBusInterface $bus)
    {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
    }

    public function __invoke(Request $request): Response
    {
        $this->bus->dispatch(new GenerateResetPasswordToken($request->request->get('email')));
        $this->bus->dispatch(new SendResetPasswordToken($request->request->get('email'), $request->attributes->get('channelCode')));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
