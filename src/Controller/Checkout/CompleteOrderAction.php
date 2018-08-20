<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\SyliusShopApiPlugin\Command\CompleteOrder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CompleteOrderAction
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param ViewHandlerInterface $viewHandler
     * @param CommandBus $bus
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(ViewHandlerInterface $viewHandler, CommandBus $bus, TokenStorageInterface $tokenStorage)
    {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $email = $this->provideUserEmail($request);

        $this->bus->handle(new CompleteOrder(
            $request->attributes->get('token'),
            $email,
            $request->request->get('notes')
        ));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function provideUserEmail(Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof ShopUserInterface) {
            return $user->getCustomer()->getEmail();
        }

        return $request->request->get('email');
    }
}
