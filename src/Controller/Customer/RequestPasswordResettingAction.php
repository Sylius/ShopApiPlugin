<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\GenerateResetPasswordToken;
use Sylius\ShopApiPlugin\Command\SendResetPasswordToken;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\ResendVerificationTokenRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
