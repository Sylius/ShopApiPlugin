<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Product;

class AddProductReviewByCode
{
    /** @var string */
    protected $productCode;

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

    public function __construct(string $productCode, string $channelCode, string $title, int $rating, string $comment, string $email)
    {
        $this->productCode = $productCode;
        $this->channelCode = $channelCode;
        $this->title = $title;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->email = $email;
    }

    public function productCode(): string
    {
        return $this->productCode;
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
