<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class AddProductReviewBySlug
{
    /**
     * @var string
     */
    private $productSlug;

    /**
     * @var string
     */
    private $channelCode;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $rating;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var string
     */
    private $email;

    public function __construct(string $productSlug, string $channelCode, string $title, int $rating, string $comment, string $email)
    {
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
