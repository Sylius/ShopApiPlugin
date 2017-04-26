<?php

namespace Sylius\ShopApiPlugin\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
                $request->request->has('billingAddress') ?
                    $request->request->get('billingAddress') :
                    $request->request->get('shippingAddress')
            )
        );

        $bus->handle($addressShipment);

        return $viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function summarizeAction(Request $request)
    {
        /** @var OrderRepositoryInterface $cartRepository */
        $cartRepository = $this->get('sylius.repository.order');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');
        /** @var CartViewFactoryInterface $cartViewFactory */
        $cartViewFactory = $this->get('sylius.shop_api_plugin.factory.cart_view_factory');
        /** @var AddressViewFactoryInterface $addressViewFactory */
        $addressViewFactory = $this->get('sylius.shop_api_plugin.factory.address_view_factory');

        /** @var OrderInterface $cart */
        $cart = $cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        $cartView = $cartViewFactory->create($cart, $cart->getLocaleCode());
        $cartView->shippingAddress = $addressViewFactory->create($cart->getShippingAddress());
        $cartView->billingAddress = $addressViewFactory->create($cart->getBillingAddress());

        return $viewHandler->handle(View::create($cartView, Response::HTTP_OK));
    }
}
