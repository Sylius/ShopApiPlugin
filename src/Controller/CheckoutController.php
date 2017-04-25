<?php

namespace Sylius\ShopApiPlugin\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckoutController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addressAction(Request $request)
    {
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');
        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');

        $addressShipment = AddressOrder::create(
            $request->attributes->get('token'),
            Address::createFromArray($request->request->get('shippingAddress')),
            Address::createFromArray(
                $request->request->has('billingAddress') ? $request->request->get('billingAddress') : $request->request->get('shippingAddress')
            )
        );

        $bus->handle($addressShipment);

        return $viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
