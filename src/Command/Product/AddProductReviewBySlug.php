<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Product;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class AddProductReviewBySlug implements CommandInterface
{
    /** @var string */
    protected $productSlug;

    /** @var string */
    protected $channelCode;

    /** @var string */
    protected $title;

    /** @var int */
    protected $rating;

    /** @var string */
    protected $comment;

    /** @var string */
    protected $email;

    public function __construct(
        string $productSlug,
        string $channelCode,
        string $title,
        int $rating,
        string $comment,
        string $email
    ) {
        $this->productSlug = $productSlug;
        $this->channelCode = $channelCode;
        $this->title = $title;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->email = $email;
    }

    public function productSlug(): string
    {
        return $this->productSlug;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function rating(): int
    {
        return $this->rating;
    }

    public function comment(): string
    {
        return $this->comment;
    }

    public function email(): string
    {
        return $this->email;
    }
}
