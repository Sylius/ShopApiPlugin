<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\ViewRepository;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\ViewRepository\ProductDetailsViewRepositoryInterface;
use Sylius\ShopApiPlugin\View\ProductView;

final class ProductDetailsViewRepositorySpec extends ObjectBehavior
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
        $this->shouldImplement(ProductDetailsViewRepositoryInterface::class);
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

        $this->findOneBySlug('logan-mug', 'WEB_GB', 'en_GB')->shouldReturn($productView);
    }

    function it_provides_product_view_by_slug_in_default_channel_locale(
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

        $this->findOneBySlug('logan-mug', 'WEB_GB', null)->shouldReturn($productView);
    }

    function it_throws_an_exception_if_requested_locale_is_not_supported_by_requested_channel(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $channel->getLocales()->willReturn([$locale]);

        $locale->getCode()->willReturn('en_GB');

        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneBySlug', ['logan-mug', 'WEB_GB', 'de_DE']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneByCode', ['LOGAN_MUG_CODE', 'WEB_GB', 'de_DE']);
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
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $channel->getLocales()->willReturn([$locale]);
        $locale->getCode()->willReturn('en_GB');

        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $productRepository->findOneByChannelAndSlug($channel, 'en_GB', 'logan-mug')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneBySlug', ['logan-mug', 'WEB_GB', 'de_DE']);
    }

    function it_provides_product_view_by_code_in_given_locale(
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

        $product->hasChannel($channel)->willReturn(true);

        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $productRepository->findOneByCode('LOGAN_MUG_CODE')->willReturn($product);

        $productViewFactory->create($product, $channel, 'en_GB')->willReturn($productView);

        $this->findOneByCode('LOGAN_MUG_CODE', 'WEB_GB', 'en_GB')->shouldReturn($productView);
    }

    function it_provides_product_view_by_code_in_default_channel_locale(
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

        $product->hasChannel($channel)->willReturn(true);

        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $productRepository->findOneByCode('LOGAN_MUG_CODE')->willReturn($product);

        $productViewFactory->create($product, $channel, 'en_GB')->willReturn($productView);

        $this->findOneByCode('LOGAN_MUG_CODE', 'WEB_GB', null)->shouldReturn($productView);
    }

    function it_throws_an_exception_if_product_was_not_found_by_code(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $channel->getLocales()->willReturn([$locale]);
        $locale->getCode()->willReturn('en_GB');

        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $productRepository->findOneByCode('LOGAN_MUG_CODE')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneByCode', ['LOGAN_MUG_CODE', 'WEB_GB', 'de_DE']);
    }

    function it_throws_an_exception_if_product_is_not_activated_in_channel(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ChannelInterface $channel,
        LocaleInterface $locale,
        ProductInterface $product
    ) {
        $channel->getLocales()->willReturn([$locale]);
        $locale->getCode()->willReturn('en_GB');

        $product->hasChannel($channel)->willReturn(false);

        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $productRepository->findOneByCode('LOGAN_MUG_CODE')->willReturn($product);

        $this->shouldThrow(\InvalidArgumentException::class)->during('findOneByCode', ['LOGAN_MUG_CODE', 'WEB_GB', 'de_DE']);
    }
}
