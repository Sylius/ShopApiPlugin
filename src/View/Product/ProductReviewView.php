<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use DateTimeInterface;

class ProductReviewView
{

    /** @var string */
    public $title;

    /** @var int */
    public $rating;

    /** @var string */
    public $comment;

    /** @var string */
    public $authorEmail;

    /** @var string */
    public $authorFirstName;

    /** @var string */
    public $authorLastName;

    /** @var DateTimeInterface */
    public $createdAt;
}
