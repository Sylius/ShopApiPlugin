<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\ViewRepository;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Sylius\ShopApiPlugin\View\ProductView;
use Sylius\ShopApiPlugin\ViewRepository\ProductDetailsViewRepositoryInterface;

final class ProductDetailsViewRepositorySpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->beConstructedWith(
            $channelRepository,
            $productRepository,
            $productViewFactory,
            $supportedLocaleProvider
        );
    }

    function it_is_product_catalog()
    {
        $this->shouldImplement(ProductDetailsViewRepositoryInterface::class);
    }

    function it_provides_product_view_by_slug_in_given_locale(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel,
        ProductInterface $product,
        ProductView $productView
    ) {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);

        $supportedLocaleProvider->provide('en_GB', $channel)->willReturn('en_GB');

        $productRepository->findOneByChannelAndSlug($channel, 'en_GB', 'logan-mug')->willReturn($product);

        $productViewFactory->create($product, $channel, 'en_GB')->willReturn($productView);

        $this->findOneBySlug('logan-mug', 'WEB_GB', 'en_GB')->shouldReturn($productView);
    }

    function it_throws_an_exception_if_the_locale_could_not_be_determined(
        ChannelRepositoryInterface $channelRepository,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ProductRepositoryInterface $productRepository,
        ChannelInterface $channel
    ) {
        $channelRepository->findOneByCode('en_US')->willReturn($channel);
        $supportedLocaleProvider->provide('de_DE', $channel)->willThrow(new \InvalidArgumentException());

        $productRepository->findOneByChannelAndSlug(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
                ->during('findOneBySlug', ['logan-mug', 'en_US', 'de_DE']);
    }

    function it_throws_an_exception_if_channel_was_not_found(ChannelRepositoryInterface $channelRepository)
    {
        $channelRepository->findOneByCode('WEB_GB')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneBySlug', ['logan-mug', 'WEB_GB', 'de_DE']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneByCode', ['LOGAN_MUG_CODE', 'WEB_GB', 'de_DE']);
    }

    function it_throws_an_exception_if_product_was_not_found_by_slug(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel
    ) {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $supportedLocaleProvider->provide('de_DE', $channel)->willReturn('en_GB');
        $productRepository->findOneByChannelAndSlug($channel, 'en_GB', 'logan-mug')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneBySlug', ['logan-mug', 'WEB_GB', 'de_DE']);
    }

    function it_provides_product_view_by_code_in_given_locale(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel,
        ProductInterface $product,
        ProductView $productView
    ) {
        $supportedLocaleProvider->provide('en_GB', $channel)->willReturn('en_GB');

        $product->hasChannel($channel)->willReturn(true);

        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $productRepository->findOneByCode('LOGAN_MUG_CODE')->willReturn($product);

        $productViewFactory->create($product, $channel, 'en_GB')->willReturn($productView);

        $this->findOneByCode('LOGAN_MUG_CODE', 'WEB_GB', 'en_GB')->shouldReturn($productView);
    }

    function it_throws_an_exception_if_product_was_not_found_by_code(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel
    ) {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $supportedLocaleProvider->provide('de_DE', $channel);

        $productRepository->findOneByCode('LOGAN_MUG_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneByCode', ['LOGAN_MUG_CODE', 'WEB_GB', 'de_DE']);
    }

    function it_throws_an_exception_if_product_is_not_activated_in_channel(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        SupportedLocaleProviderInterface $supportedLocaleProvider,
        ChannelInterface $channel,
        ProductInterface $product
    ) {
        $product->hasChannel($channel)->willReturn(false);

        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $supportedLocaleProvider->provide('de_DE', $channel);
        $productRepository->findOneByCode('LOGAN_MUG_CODE')->willReturn($product);

        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneByCode', ['LOGAN_MUG_CODE', 'WEB_GB', 'de_DE']);
    }
}
