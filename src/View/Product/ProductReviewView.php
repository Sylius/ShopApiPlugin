<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public $author;

    /** @var DateTimeInterface */
    public $createdAt;
}
