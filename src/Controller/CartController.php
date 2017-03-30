<?php

namespace Sylius\ShopApiPlugin\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CartController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        /** @var FactoryInterface $cartFactory */
        $cartFactory = $this->get('sylius.factory.order');
        /** @var OrderRepositoryInterface $cartRepository */
        $cartRepository = $this->get('sylius.repository.order');
        /** @var ChannelRepositoryInterface $channelRepository */
        $channelRepository = $this->get('sylius.repository.channel');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');

        /** @var ChannelInterface $channel */
        $channel = $channelRepository->findOneByCode($request->request->get('channel'));

        /** @var OrderInterface $cart */
        $cart = $cartFactory->createNew();
        $cart->setChannel($channel);
        $cart->setCurrencyCode($channel->getBaseCurrency()->getCode());
        $cart->setLocaleCode($channel->getDefaultLocale()->getCode());
        $cart->setTokenValue($request->query->get('token'));

        $cartRepository->add($cart);

        return $viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }
}
