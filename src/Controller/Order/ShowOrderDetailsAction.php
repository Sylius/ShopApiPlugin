<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Order;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInUserProviderInterface;
use Sylius\ShopApiPlugin\ViewRepository\PlacedOrderViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class ShowOrderDetailsAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var LoggedInUserProviderInterface */
    private $loggedInUserProvider;

    /** @var PlacedOrderViewRepositoryInterface */
    private $placedOrderQuery;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        LoggedInUserProviderInterface $loggedInUserProvider,
        PlacedOrderViewRepositoryInterface $placedOrderQuery
    ) {
        $this->viewHandler          = $viewHandler;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->placedOrderQuery     = $placedOrderQuery;
    }

    public function __invoke(Request $request): Response
    {
        try {
            /** @var ShopUserInterface $user */
            $user = $this->loggedInUserProvider->provide();

            // It is either "id" or "tokenValue". Other routes dont even get here or get cancelled with 404 no-route
            if ($request->attributes->has('id')) {
                $order = $this->placedOrderQuery
                    ->getOneCompletedByCustomerEmailAndId(
                        $user->getCustomer()->getEmail(),
                        $request->attributes->get('id')
                    );
            } else {
                $order = $this->placedOrderQuery
                    ->getOneCompletedByCustomerEmailAndToken(
                        $user->getCustomer()->getEmail(),
                        $request->attributes->get('tokenValue')
                    );
            }

        } catch (TokenNotFoundException $exception) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }

        return $this->viewHandler->handle(View::create($order, Response::HTTP_OK));
    }
}
