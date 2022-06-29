<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Product;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductReviewerInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\ShopApiPlugin\Command\Product\AddProductReviewBySlug;
use Sylius\ShopApiPlugin\Handler\Product\AddProductReviewBySlugHandler;
use Sylius\ShopApiPlugin\Provider\ProductReviewerProviderInterface;

final class AddProductReviewBySlugHandlerSpec extends ObjectBehavior
{
    function let(
        ProductReviewRepositoryInterface $productReviewRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductReviewerProviderInterface $productReviewerProvider,
        ReviewFactoryInterface $reviewFactory,
    ): void {
        $this->beConstructedWith($productReviewRepository, $channelRepository, $productRepository, $productReviewerProvider, $reviewFactory);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductReviewBySlugHandler::class);
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
        ReviewInterface $productReview,
    ): void {
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

        $this(new AddProductReviewBySlug('logan-mug', 'WEB_GB', 'Perfect product', 5, 'It is so awesome :)', 'example@shop.com'));
    }

    function it_throws_an_exception_if_channel_has_not_been_found(ChannelRepositoryInterface $channelRepository): void
    {
        $channelRepository->findOneByCode('WEB_GB')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new AddProductReviewBySlug('logan-mug', 'WEB_GB', 'Perfect product', 5, 'It is so awesome :)', 'example@shop.com'),
            ])
        ;
    }

    function it_throws_an_exception_if_product_has_not_been_found(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): void {
        $channelRepository->findOneByCode('WEB_GB')->willReturn($channel);
        $channel->getDefaultLocale()->willReturn($locale);
        $locale->getCode()->willReturn('en_GB');

        $productRepository->findOneByChannelAndSlug($channel, 'en_GB', 'logan-mug')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new AddProductReviewBySlug('logan-mug', 'WEB_GB', 'Perfect product', 5, 'It is so awesome :)', 'example@shop.com'),
            ])
        ;
    }
}
