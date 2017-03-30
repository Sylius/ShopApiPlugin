<?php

namespace Sylius\ShopApiPlugin\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Sylius\ShopApiPlugin\View\ItemView;
use Sylius\ShopApiPlugin\View\ProductView;
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
        $cartView->totals->items = $cart->getItemsTotal();
        $cartView->totals->shipping = $cart->getShippingTotal();
        $cartView->totals->taxes = $cart->getTaxTotal();

        /** @var OrderItemInterface $item */
        foreach ($cart->getItems() as $item) {
            $itemView = new ItemView();

            $itemView->id = $item->getId();
            $itemView->quantity = $item->getQuantity();
            $itemView->total = $item->getTotal();
            $itemView->unitPrice = $item->getUnitPrice();
            $itemView->product = new ProductView();
            $itemView->product->code = $item->getProduct()->getCode();
            $itemView->product->name = $item->getProduct()->getName();

            $cartView->items[] = $itemView;
        }

        return $viewHandler->handle(View::create($cartView, Response::HTTP_OK));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        /** @var OrderRepositoryInterface $cartRepository */
        $cartRepository = $this->get('sylius.repository.order');
        /** @var ObjectManager $cartManager */
        $cartManager = $this->get('sylius.manager.order');
        /** @var ProductVariantRepositoryInterface $productRepository */
        $productRepository = $this->get('sylius.repository.product_variant');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');
        /** @var CartItemFactoryInterface $cartItemFactory */
        $cartItemFactory = $this->get('sylius.factory.order_item');
        /** @var OrderItemQuantityModifierInterface $orderItemModifier */
        $orderItemModifier = $this->get('sylius.order_item_quantity_modifier');
        /** @var OrderProcessorInterface $orderProcessor */
        $orderProcessor = $this->get('sylius.order_processing.order_processor');

        /** @var OrderInterface $cart */
        $cart = $cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);
        $productVariant = $productRepository->findOneBy(['code' => $request->request->get('code')]);
        /** @var OrderItemInterface $cartItem */
        $cartItem = $cartItemFactory->createForCart($cart);
        $cartItem->setVariant($productVariant);
        $orderItemModifier->modify($cartItem, $request->request->getInt('quantity'));

        $cart->addItem($cartItem);

        $orderProcessor->process($cart);

        $cartManager->flush();

        return $viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }
}
