<?php

namespace Sylius\ShopApiPlugin\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Sylius\ShopApiPlugin\View\EstimatedShippingCostView;
use Sylius\ShopApiPlugin\View\ItemView;
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
        /** @var ProductViewFactoryInterface $productViewFactory */
        $productViewFactory = $this->get('sylius.shop_api_plugin.factory.product_view_factory');
        /** @var ProductVariantViewFactoryInterface $productViewViewFactory */
        $productViewViewFactory = $this->get('sylius.shop_api_plugin.factory.product_variant_view_factory');

        /** @var OrderInterface $cart */
        $cart = $cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        $locale = $cart->getLocaleCode();

        $cartView = new CartSummaryView();
        $cartView->channel = $cart->getChannel()->getCode();
        $cartView->currency = $cart->getCurrencyCode();
        $cartView->locale = $locale;
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
            $product = $item->getProduct();

            $itemView->id = $item->getId();
            $itemView->quantity = $item->getQuantity();
            $itemView->total = $item->getTotal();
            $itemView->product = $productViewFactory->create($product, $locale);
            $itemView->product->variants = [$productViewViewFactory->create($item->getVariant(), $cart->getChannel(), $locale)];

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

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        $productVariant = $this->resolveVariant($request);

        if (null === $productVariant) {
            throw new NotFoundHttpException('Variant not found for given configuration');
        }

        /** @var OrderItemInterface $cartItem */
        $cartItem = $cartItemFactory->createForCart($cart);
        $cartItem->setVariant($productVariant);
        $orderItemModifier->modify($cartItem, $request->request->getInt('quantity'));

        $cart->addItem($cartItem);

        $orderProcessor->process($cart);

        $cartManager->flush();

        return $viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function dropAction(Request $request)
    {
        /** @var OrderRepositoryInterface $cartRepository */
        $cartRepository = $this->get('sylius.repository.order');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');

        $cart = $cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        $cartRepository->remove($cart);

        return $viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function changeItemQuantityAction(Request $request)
    {
        /** @var ObjectManager $cartManager */
        $cartManager = $this->get('sylius.manager.order');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');
        /** @var OrderItemRepositoryInterface $orderItemRepository */
        $cartItemRepository = $this->get('sylius.repository.order_item');
        /** @var OrderItemQuantityModifierInterface $orderItemModifier */
        $orderItemModifier = $this->get('sylius.order_item_quantity_modifier');
        /** @var OrderProcessorInterface $orderProcessor */
        $orderProcessor = $this->get('sylius.order_processing.order_processor');
        /** @var OrderRepositoryInterface $cartRepository */
        $cartRepository = $this->get('sylius.repository.order');

        $cart = $cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        /** @var OrderInterface $cart */
        $cartItem = $cartItemRepository->find($request->attributes->get('id'));

        if (null === $cartItem || !$cart->hasItem($cartItem)) {
            throw new NotFoundHttpException('Cart item with given id does not exists');
        }

        $orderItemModifier->modify($cartItem, $request->request->getInt('quantity'));

        $orderProcessor->process($cart);

        $cartManager->flush();

        return $viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function removeItemAction(Request $request)
    {
        /** @var OrderRepositoryInterface $cartRepository */
        $cartRepository = $this->get('sylius.repository.order');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');
        /** @var OrderItemRepositoryInterface $orderItemRepository */
        $cartItemRepository = $this->get('sylius.repository.order_item');

        $cart = $cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        /** @var OrderInterface $cart */
        $cartItem = $cartItemRepository->find($request->attributes->get('id'));

        if (null === $cartItem || !$cart->hasItem($cartItem)) {
            throw new NotFoundHttpException('Cart item with given id does not exists');
        }

        $cart->removeItem($cartItem);
        $cartItemRepository->remove($cartItem);

        return $viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function estimateShippingCostAction(Request $request)
    {
        /** @var OrderRepositoryInterface $cartRepository */
        $cartRepository = $this->get('sylius.repository.order');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');
        /** @var ShippingMethodsResolverInterface $shippingMethodResolver */
        $shippingMethodResolver = $this->get('sylius.shipping_methods_resolver');
        /** @var AddressFactoryInterface $addressFactory */
        $addressFactory = $this->get('sylius.factory.address');
        /** @var FactoryInterface $shipmentFactory */
        $shipmentFactory = $this->get('sylius.factory.shipment');
        /** @var ServiceRegistryInterface $calculators */
        $calculators = $this->get('sylius.registry.shipping_calculator');
        /** @var PriceViewFactoryInterface $priceViewFactory */
        $priceViewFactory = $this->get('sylius.shop_api_plugin.factory.price_view_factory');

        /** @var OrderInterface $cart */
        $cart = $cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        /** @var AddressInterface $address */
        $address = $addressFactory->createNew();
        $address->setCountryCode($request->query->get('countryCode'));
        $address->setProvinceCode($request->query->get('provinceCode'));
        $cart->setShippingAddress($address);

        /** @var ShipmentInterface $shipment */
        $shipment = $shipmentFactory->createNew();
        $shipment->setOrder($cart);

        $shippingMethods = $shippingMethodResolver->getSupportedMethods($shipment);

        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }

        $shippingMethod = $shippingMethods[0];

        $estimatedShippingCostView = new EstimatedShippingCostView();
        $calculator = $calculators->get($shippingMethod->getCalculator());

        $estimatedShippingCostView->price = $priceViewFactory->create($calculator->calculate($shipment, $shippingMethod->getConfiguration()));

        return $viewHandler->handle(View::create($estimatedShippingCostView, Response::HTTP_OK));
    }

    /**
     * @param Request $request
     *
     * @return null|ProductVariantInterface
     */
    private function resolveVariant(Request $request)
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->get('sylius.repository.product');

        /** @var ProductInterface $product */
        $product = $productRepository->findOneBy(['code' => $request->request->get('productCode')]);

        if ($product->isSimple()) {
            return $product->getVariants()[0];
        }

        if ($product->hasOptions()){
            return $this->getVariant($request->request->get('options'), $product);
        }

        /** @var ProductVariantRepositoryInterface $productVariantRepository */
        $productVariantRepository = $this->get('sylius.repository.product_variant');

        return $productVariantRepository->findOneByCodeAndProductCode($request->request->get('variantCode'), $request->request->get('productCode'));
    }

    /**
     * @param array $options
     * @param ProductInterface $product
     *
     * @return null|ProductVariantInterface
     */
    private function getVariant(array $options, ProductInterface $product)
    {
        foreach ($product->getVariants() as $variant) {
            foreach ($variant->getOptionValues() as $optionValue) {
                if (!isset($options[$optionValue->getOptionCode()]) || $optionValue->getCode() !== $options[$optionValue->getOptionCode()]) {
                    continue 2;
                }
            }

            return $variant;
        }

        return null;
    }
}
