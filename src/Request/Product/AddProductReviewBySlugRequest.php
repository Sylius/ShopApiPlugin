<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Product;

use Sylius\ShopApiPlugin\Command\Product\AddProductReviewBySlug;
use Symfony\Component\HttpFoundation\Request;

class AddProductReviewBySlugRequest
{
    /** @var string */
    protected $slug;

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

    public function __construct(Request $request, string $channelCode)
    {
        $this->slug = $request->attributes->get('slug');
        $this->title = $request->request->get('title');
        $this->rating = $request->request->get('rating');
        $this->comment = $request->request->get('comment');
        $this->email = $request->request->get('email');

        $this->channelCode = $channelCode;
    }

    public function getCommand(): AddProductReviewBySlug
    {
        return new AddProductReviewBySlug(
            $this->slug,
            $this->channelCode,
            $this->title,
            $this->rating,
            $this->comment,
            $this->email
        );
    }
}
