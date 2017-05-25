<?php

namespace Sylius\ShopApiPlugin\Controller\Cart;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PutItemToCartAction
{
    /**
     * @var OrderRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ObjectManager
     */
    private $cartManager;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var CartItemFactoryInterface
     */
    private $cartItemFactory;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemModifier;

    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @param OrderRepositoryInterface $cartRepository
     * @param ObjectManager $cartManager
     * @param ViewHandlerInterface $viewHandler
     * @param CartItemFactoryInterface $cartItemFactory
     * @param OrderItemQuantityModifierInterface $orderItemModifier
     * @param OrderProcessorInterface $orderProcessor
     * @param ProductRepositoryInterface $productRepository
     * @param ProductVariantRepositoryInterface $productVariantRepository
     * @param CommandBus $bus
     */
    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ObjectManager $cartManager,
        ViewHandlerInterface $viewHandler,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        CommandBus $bus
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartManager = $cartManager;
        $this->viewHandler = $viewHandler;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemModifier = $orderItemModifier;
        $this->orderProcessor = $orderProcessor;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->bus = $bus;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        if (!$request->request->has('variantCode') && !$request->request->has('options')) {
            $this->bus->handle(new PutSimpleItemToCart(
                $request->attributes->get('token'),
                $request->request->get('productCode'),
                $request->request->getInt('quantity')
            ));

            return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
        }

        if ($request->request->has('variantCode') && !$request->request->has('options')) {
            $this->bus->handle(new PutVariantBasedConfigurableItemToCart(
                $request->attributes->get('token'),
                $request->request->get('productCode'),
                $request->request->get('variantCode'),
                $request->request->getInt('quantity')
            ));

            return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
        }

        $productVariant = $this->resolveVariant($request);

        if (null === $productVariant) {
            throw new NotFoundHttpException('Variant not found for given configuration');
        }

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createForCart($cart);
        $cartItem->setVariant($productVariant);
        $this->orderItemModifier->modify($cartItem, $request->request->getInt('quantity'));

        $cart->addItem($cartItem);

        $this->orderProcessor->process($cart);

        $this->cartManager->flush();

        return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }

    /**
     * @param Request $request
     *
     * @return null|ProductVariantInterface
     */
    private function resolveVariant(Request $request)
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy(['code' => $request->request->get('productCode')]);

        return $this->getVariant($request->request->get('options'), $product);
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
