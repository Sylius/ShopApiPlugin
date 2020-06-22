<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Product;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Sylius\ShopApiPlugin\Command\Product\AddProductReviewByCode;
use App\Event\ProductReviewCreated;
use Sylius\ShopApiPlugin\Provider\ProductReviewerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;


final class AddProductReviewByCodeHandler
{
    /** @var ProductReviewRepositoryInterface */
    private $productReviewRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductReviewerProviderInterface */
    private $productReviewerProvider;

    /** @var ReviewFactoryInterface */
    private $reviewFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        ProductReviewRepositoryInterface $productReviewRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductReviewerProviderInterface $productReviewerProvider,
        ReviewFactoryInterface $reviewFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->productReviewRepository = $productReviewRepository;
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->productReviewerProvider = $productReviewerProvider;
        $this->reviewFactory = $reviewFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(AddProductReviewByCode $addReview): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($addReview->channelCode());

        Assert::notNull($channel, 'Channel not found.');

        $product = $this->productRepository->findOneByCode($addReview->productCode());

        Assert::notNull($product, 'Product not found.');
        Assert::true($product->hasChannel($channel), 'Product is not enabled for given channel.');

        $productReviewer = $this->productReviewerProvider->provide($addReview->email());

        $productReview = $this->reviewFactory->createForSubjectWithReviewer($product, $productReviewer);

        $productReview->setComment($addReview->comment());
        $productReview->setRating($addReview->rating());
        $productReview->setTitle($addReview->title());

        $this->productReviewRepository->add($productReview);

        $this->eventDispatcher->dispatch('sylius.product_review.post_create', new ProductReviewCreated(
            $productReview->getReviewSubject()->getName()
        ));


    }
}
