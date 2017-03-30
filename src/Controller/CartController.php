<?php

namespace Sylius\ShopApiPlugin\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Sylius\ShopApiPlugin\View\TotalsView;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CartController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function pickupAction(Request $request)
    {
        /** @var FactoryInterface $cartFactory */
        $cartFactory = $this->get('sylius.factory.order');
        /** @var OrderRepositoryInterface $cartRepository */
        $cartRepository = $this->get('sylius.repository.order');
        /** @var ChannelRepositoryInterface $channelRepository */
        $channelRepository = $this->get('sylius.repository.channel');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');

        if (null !== $cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')])) {
            throw new BadRequestHttpException('Cart with given token already exists');
        }

        /** @var ChannelInterface $channel */
        $channel = $channelRepository->findOneByCode($request->request->get('channel'));

        /** @var OrderInterface $cart */
        $cart = $cartFactory->createNew();
        $cart->setChannel($channel);
        $cart->setCurrencyCode($channel->getBaseCurrency()->getCode());
        $cart->setLocaleCode($channel->getDefaultLocale()->getCode());
        $cart->setTokenValue($request->attributes->get('token'));

        $cartRepository->add($cart);

        return $viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function summaryAction(Request $request)
    {
        /** @var OrderRepositoryInterface $cartRepository */
        $cartRepository = $this->get('sylius.repository.order');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');

        /** @var OrderInterface $cart */
        $cart = $cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        $cartView = new CartSummaryView();
        $cartView->channel = $cart->getChannel()->getCode();
        $cartView->currency = $cart->getCurrencyCode();
        $cartView->locale = $cart->getLocaleCode();
        $cartView->checkoutState = $cart->getCheckoutState();
        $cartView->tokenValue = $cart->getTokenValue();
        $cartView->totals = new TotalsView();
        $cartView->totals->promotion = 0;
        $cartView->totals->items = 0;
        $cartView->totals->shipping = 0;
        $cartView->totals->taxes = 0;

        return $viewHandler->handle(View::create($cartView, Response::HTTP_OK));
    }
}
