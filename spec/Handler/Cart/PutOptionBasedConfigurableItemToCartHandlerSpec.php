<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\ShopApiPlugin\Checker\ProductInCartChannelCheckerInterface;
use Sylius\ShopApiPlugin\Command\Cart\PutOptionBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Modifier\OrderModifierInterface;

final class PutOptionBasedConfigurableItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier,
        ProductInCartChannelCheckerInterface $channelChecker
    ): void {
        $this->beConstructedWith($orderRepository, $productRepository, $orderModifier, $channelChecker);
    }

    function it_handles_putting_new_item_to_cart(
        OrderInterface $cart,
        OrderRepositoryInterface $orderRepository,
        OrderModifierInterface $orderModifier,
        ProductInCartChannelCheckerInterface $channelChecker,
        ProductInterface $tShirt,
        ProductOptionValueInterface $blueOptionValue,
        ProductOptionValueInterface $redOptionValue,
        ProductRepositoryInterface $productRepository,
        ProductVariantInterface $blueTShirt,
        ProductVariantInterface $redTShirt
    ): void {
        $productRepository->findOneByCode('T_SHIRT_CODE')->willReturn($tShirt);

        $tShirt->getVariants()->willReturn(new ArrayCollection([
            $blueTShirt->getWrappedObject(),
            $redTShirt->getWrappedObject(),
        ]));

        $blueTShirt->getOptionValues()->willReturn(new ArrayCollection([$blueOptionValue->getWrappedObject()]));
        $blueOptionValue->getCode()->willReturn('BLUE_OPTION_VALUE_CODE');
        $blueOptionValue->getOptionCode()->willReturn('COLOR_OPTION_CODE');
        $channelChecker->isProductInCartChannel($tShirt, $cart)->willReturn(true);

        $redTShirt->getOptionValues()->willReturn(new ArrayCollection([$redOptionValue->getWrappedObject()]));
        $redOptionValue->getCode()->willReturn('RED_OPTION_VALUE_CODE');
        $redOptionValue->getOptionCode()->willReturn('COLOR_OPTION_CODE');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $orderModifier->modify($cart, $redTShirt, 5)->shouldBeCalled();

        $this(new PutOptionBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', ['COLOR_OPTION_CODE' => 'RED_OPTION_VALUE_CODE'], 5));
    }

    function it_throws_an_exception_if_cart_has_not_been_found(OrderRepositoryInterface $orderRepository): void
    {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutOptionBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', ['COLOR_OPTION_CODE' => 'RED_OPTION_VALUE_CODE'], 5),
        ]);
    }

    function it_throws_an_exception_if_product_has_not_been_found(
        OrderInterface $cart,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $productRepository->findOneByCode('T_SHIRT_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutOptionBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', ['COLOR_OPTION_CODE' => 'RED_OPTION_VALUE_CODE'], 5),
        ]);
    }

    function it_throws_an_exception_if_product_variant_cannot_be_resolved(
        OrderInterface $cart,
        CartItemFactoryInterface $cartItemFactory,
        OrderRepositoryInterface $orderRepository,
        ProductInCartChannelCheckerInterface $channelChecker,
        ProductInterface $tShirt,
        ProductVariantInterface $blueTShirt,
        ProductVariantInterface $redTShirt,
        ProductOptionValueInterface $blueOptionValue,
        ProductOptionValueInterface $redOptionValue,
        ProductRepositoryInterface $productRepository
    ): void {
        $productRepository->findOneByCode('T_SHIRT_CODE')->willReturn($tShirt);

        $tShirt->getVariants()->willReturn(new ArrayCollection([
            $blueTShirt->getWrappedObject(),
            $redTShirt->getWrappedObject(),
        ]));

        $blueTShirt->getOptionValues()->willReturn(new ArrayCollection([$blueOptionValue->getWrappedObject()]));
        $blueOptionValue->getCode()->willReturn('BLUE_OPTION_VALUE_CODE');
        $blueOptionValue->getOptionCode()->willReturn('COLOR_OPTION_CODE');
        $channelChecker->isProductInCartChannel($tShirt, $cart);

        $redTShirt->getOptionValues()->willReturn(new ArrayCollection([$redOptionValue->getWrappedObject()]));
        $redOptionValue->getCode()->willReturn('GREEN_OPTION_VALUE_CODE');
        $redOptionValue->getOptionCode()->willReturn('COLOR_OPTION_CODE');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cartItemFactory->createForCart($cart)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutOptionBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', ['COLOR_OPTION_CODE' => 'RED_OPTION_VALUE_CODE'], 5),
        ]);
    }

    function it_throws_an_exception_if_product_is_not_in_same_channel_as_cart(
        OrderInterface $cart,
        OrderRepositoryInterface $orderRepository,
        ProductInCartChannelCheckerInterface $channelChecker,
        ProductInterface $tShirt,
        ProductRepositoryInterface $productRepository
    ): void {
        $productRepository->findOneByCode('T_SHIRT_CODE')->willReturn($tShirt);

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $channelChecker->isProductInCartChannel($tShirt, $cart)->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [
            new PutOptionBasedConfigurableItemToCart('ORDERTOKEN', 'T_SHIRT_CODE', ['COLOR_OPTION_CODE' => 'RED_OPTION_VALUE_CODE'], 5),
        ]);
    }
}
