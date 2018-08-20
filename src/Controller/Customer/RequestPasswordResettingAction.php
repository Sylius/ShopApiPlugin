<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\SyliusShopApiPlugin\Command\GenerateResetPasswordToken;
use Sylius\SyliusShopApiPlugin\Command\SendResetPasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequestPasswordResettingAction
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var CommandBus
     */
    private $bus;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
    }

    public function __invoke(Request $request): Response
    {
        $this->bus->handle(new GenerateResetPasswordToken($request->request->get('email')));
        $this->bus->handle(new SendResetPasswordToken($request->request->get('email')));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
