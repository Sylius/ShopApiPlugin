<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class LoggedInCustomerDetailsAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(ViewHandlerInterface $viewHandler, TokenStorageInterface $tokenStorage)
    {
        $this->viewHandler = $viewHandler;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ShopUserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();

        Assert::isInstanceOf($user, ShopUserInterface::class);

        $customer = $user->getCustomer();

        return $this->viewHandler->handle(View::create([
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
        ], Response::HTTP_OK));
    }
}
