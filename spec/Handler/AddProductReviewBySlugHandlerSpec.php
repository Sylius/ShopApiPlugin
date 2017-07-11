<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductReviewerInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\ShopApiPlugin\Command\AddProductReviewBySlug;
use Sylius\ShopApiPlugin\Handler\AddProductReviewBySlugHandler;
use Sylius\ShopApiPlugin\Provider\ProductReviewerProviderInterface;

final class AddProductReviewBySlugHandlerSpec extends ObjectBehavior
{
    function let(
        ProductReviewRepositoryInterface $productReviewRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductReviewerProviderInterface $productReviewerProvider,
        ReviewFactoryInterface $reviewFactory
    ) {
        $this->beConstructedWith($productReviewRepository, $channelRepository, $productRepository, $productReviewerProvider, $reviewFactory);
    }

    function it_handles_adding_product_review_with_author(
        ProductReviewRepositoryInterface $productReviewRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductReviewerProviderInterface $productReviewerProvider,
        ReviewFactoryInterface $reviewFactory,
        ProductInterface $product,
        ChannelInterface $channel,
        ProductReviewerInterface $productReviewer,
        LocaleInterface $locale,
        ReviewInterface $productReview
    ) {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $channel->getDefaultLocale()->willReturn($locale);
        $locale->getCode()->willReturn('en_GB');

        $productRepository->findOneByChannelAndSlug($channel, 'en_GB', 'logan-mug')->willReturn($product);
        $reviewFactory->createForSubjectWithReviewer($product, $productReviewer)->willReturn($productReview);

        $productReviewerProvider->provide('example@shop.com')->willReturn($productReviewer);

        $productReview->setComment('It is so awesome :)')->shouldBeCalled();
        $productReview->setRating(5)->shouldBeCalled();
        $productReview->setTitle('Perfect product')->shouldBeCalled();

        $productReviewRepository->add($productReview)->shouldBeCalled();

        $this->handle(new AddProductReviewBySlug('logan-mug','WEB_GB', 'Perfect product', 5, 'It is so awesome :)', 'example@shop.com'));
    }

    function it_throws_an_exception_if_channel_has_not_been_found(ChannelRepositoryInterface $channelRepository)
    {
        $channelRepository->findOneByCode('WEB_GB')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
                new AddProductReviewBySlug('logan-mug','WEB_GB', 'Perfect product', 5, 'It is so awesome :)', 'example@shop.com')
            ])
        ;
    }

    function it_throws_an_exception_if_product_has_not_been_found(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $channel->getDefaultLocale()->willReturn($locale);
        $locale->getCode()->willReturn('en_GB');

        $productRepository->findOneByChannelAndSlug($channel, 'en_GB', 'logan-mug')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
                new AddProductReviewBySlug('logan-mug','WEB_GB', 'Perfect product', 5, 'It is so awesome :)', 'example@shop.com')
            ])
        ;
    }
}
