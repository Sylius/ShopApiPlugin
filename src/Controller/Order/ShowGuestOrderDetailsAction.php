<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Order;


use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\Traits\CustomerGuestAuthenticationInterface;
use Sylius\ShopApiPlugin\ViewRepository\Order\PlacedOrderViewRepositoryInterface;
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

    /** @var PlacedOrderViewRepositoryInterface */
    protected $placedOrderViewRepository;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        TokenStorageInterface $tokenStorage,
        PlacedOrderViewRepositoryInterface $placedOrderViewRepository
    ) {
        $this->viewHandler = $viewHandler;
        $this->tokenStorage = $tokenStorage;
        $this->placedOrderViewRepository = $placedOrderViewRepository;
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

            $order = $this->placedOrderViewRepository->getOneCompletedByCustomerEmailAndToken($customer->getEmail(), $customer->getAuthorizedOrder()->getTokenValue());
        } catch (\InvalidArgumentException $exception) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        return $this->viewHandler->handle(View::create($order, Response::HTTP_OK));
    }
}
