<?php

namespace spec\Sylius\ShopApiPlugin\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\ShopApiPlugin\Command\PutOptionBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Handler\PutOptionBasedConfigurableItemToCartHandler;
use PhpSpec\ObjectBehavior;

final class PutOptionBasedConfigurableItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor,
        ObjectManager $manager
    ) {
        $this->beConstructedWith($cartRepository, $productRepository, $cartItemFactory, $orderItemModifier, $orderProcessor, $manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PutOptionBasedConfigurableItemToCartHandler::class);
    }

    function it_handles_putting_new_item_to_cart(
        OrderItemInterface $cartItem,
        OrderInterface $cart,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor,
        OrderRepositoryInterface $cartRepository,
        ProductInterface $tShirt,
        ProductVariantInterface $blueTShirt,
        ProductVariantInterface $redTShirt,
        ProductOptionValueInterface $blueOption,
        ProductOptionValueInterface $redOption,
        ProductRepositoryInterface $productRepository,
        ObjectManager $manager
    ) {
        $productRepository->findOneByCode('T_SHIRT_CODE')->willReturn($tShirt);

        $tShirt->getVariants()->willReturn([$blueTShirt, $redTShirt]);

        $blueTShirt->getOptionValues()->willReturn([$blueOption]);
        $blueOption->getCode()->willReturn('BLUE_OPTION_VALUE_CODE');
        $blueOption->getOptionCode()->willReturn('COLOR_OPTION_CODE');

        $redTShirt->getOptionValues()->willReturn([$redOption]);
        $redOption->getCode()->willReturn('RED_OPTION_VALUE_CODE');
        $redOption->getOptionCode()->willReturn('COLOR_OPTION_CODE');

        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cartItemFactory->createForCart($cart)->willReturn($cartItem);

        $cartItem->setVariant($redTShirt)->shouldBeCalled();
        $orderItemModifier->modify($cartItem, 5)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $manager->persist($cart)->shouldBeCalled();

        $this->handle(new PutOptionBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', ['COLOR_OPTION_CODE' => 'RED_OPTION_VALUE_CODE'], 5));
    }

    function it_throws_an_exception_if_cart_has_not_been_found(OrderRepositoryInterface $cartRepository)
    {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new PutOptionBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', ['COLOR_OPTION_CODE' => 'RED_OPTION_VALUE_CODE'], 5),
        ]);
    }

    function it_throws_an_exception_if_product_has_not_been_found(
        OrderInterface $cart,
        OrderRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productRepository->findOneByCode('T_SHIRT_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new PutOptionBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', ['COLOR_OPTION_CODE' => 'RED_OPTION_VALUE_CODE'], 5),
        ]);
    }

    function it_throws_an_exception_if_product_variant_cannot_be_resolved(
        OrderInterface $cart,
        CartItemFactoryInterface $cartItemFactory,
        OrderRepositoryInterface $cartRepository,
        ProductInterface $tShirt,
        ProductVariantInterface $blueTShirt,
        ProductVariantInterface $redTShirt,
        ProductOptionValueInterface $blueOption,
        ProductOptionValueInterface $redOption,
        ProductRepositoryInterface $productRepository
    ) {
        $productRepository->findOneByCode('T_SHIRT_CODE')->willReturn($tShirt);

        $tShirt->getVariants()->willReturn([$blueTShirt, $redTShirt]);

        $blueTShirt->getOptionValues()->willReturn([$blueOption]);
        $blueOption->getCode()->willReturn('BLUE_OPTION_VALUE_CODE');
        $blueOption->getOptionCode()->willReturn('COLOR_OPTION_CODE');

        $redTShirt->getOptionValues()->willReturn([$redOption]);
        $redOption->getCode()->willReturn('GREEN_OPTION_VALUE_CODE');
        $redOption->getOptionCode()->willReturn('COLOR_OPTION_CODE');

        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cartItemFactory->createForCart($cart)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new PutOptionBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', ['COLOR_OPTION_CODE' => 'RED_OPTION_VALUE_CODE'], 5),
        ]);
    }
}
