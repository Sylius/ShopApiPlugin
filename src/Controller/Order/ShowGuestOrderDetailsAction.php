<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Order;


use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\Traits\CustomerGuestAuthenticationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class ShowGuestOrderDetailsAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        TokenStorageInterface $tokenStorage
    ) {
        $this->viewHandler = $viewHandler;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $token = $this->tokenStorage->getToken();

            Assert::notNull($token);

            /** @var CustomerGuestAuthenticationInterface|CustomerInterface $customer */
            $customer = $token->getUser();

            Assert::isInstanceOf($customer, CustomerGuestAuthenticationInterface::class);
            Assert::null($customer->getUser());

            $order = $customer->getAuthorizedOrder();
        } catch (\InvalidArgumentException $exception) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        return $this->viewHandler->handle(View::create($order, Response::HTTP_OK));
    }
}
