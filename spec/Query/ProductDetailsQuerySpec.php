<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Query;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Query\ProductDetailsQueryInterface;
use Sylius\ShopApiPlugin\View\ProductView;

final class ProductDetailsQuerySpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory
    ) {
        $this->beConstructedWith($channelRepository, $productRepository, $productViewFactory);
    }

    function it_is_product_catalog()
    {
        $this->shouldImplement(ProductDetailsQueryInterface::class);
    }

    function it_provides_product_view_by_slug_in_given_locale(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory,
        ChannelInterface $channel,
        LocaleInterface $locale,
        ProductInterface $product,
        ProductView $productView
    ) {
        $channel->getLocales()->willReturn([$locale]);
        $locale->getCode()->willReturn('en_GB');

        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $productRepository->findOneByChannelAndSlug($channel, 'en_GB', 'logan-mug')->willReturn($product);

        $productViewFactory->create($product, $channel, 'en_GB')->willReturn($productView);

        $this->findOneBySlug('WEB_GB', 'logan-mug', 'en_GB')->shouldReturn($productView);
    }

    function it_provides_product_view_by_slug_in_defaul_channel_locale(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory,
        ChannelInterface $channel,
        LocaleInterface $locale,
        ProductInterface $product,
        ProductView $productView
    ) {
        $channel->getLocales()->willReturn([$locale]);

        $channel->getDefaultLocale()->willReturn($locale);
        $locale->getCode()->willReturn('en_GB');
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $productRepository->findOneByChannelAndSlug($channel, 'en_GB', 'logan-mug')->willReturn($product);

        $productViewFactory->create($product, $channel, 'en_GB')->willReturn($productView);

        $this->findOneBySlug('WEB_GB', 'logan-mug', null)->shouldReturn($productView);
    }

    function it_throws_an_exception_if_requested_locale_is_not_supported_by_requested_channel(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $channel->getLocales()->willReturn([$locale]);

        $locale->getCode()->willReturn('en_GB');

        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneBySlug', ['WEB_GB', 'logan-mug', 'de_DE']);
    }
}
