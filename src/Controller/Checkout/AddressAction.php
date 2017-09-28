<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddressAction
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
     * @param ViewHandlerInterface $viewHandler
     * @param CommandBus $bus
     */
    public function __construct(ViewHandlerInterface $viewHandler, CommandBus $bus)
    {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $this->bus->handle(new AddressOrder(
            $request->attributes->get('token'),
            Address::createFromArray($request->request->get('shippingAddress')),
            Address::createFromArray($request->request->get('billingAddress') ?: $request->request->get('shippingAddress'))
        ));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
