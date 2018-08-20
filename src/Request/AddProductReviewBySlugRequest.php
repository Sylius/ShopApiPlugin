<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\AddProductReviewBySlug;
use Symfony\Component\HttpFoundation\Request;

final class AddProductReviewBySlugRequest
{
    /** @var string */
    private $slug;

    /** @var string */
    private $channelCode;

    /** @var string */
    private $title;

    /** @var int */
    private $rating;

    /** @var string */
    private $comment;

    /** @var string */
    private $email;

    public function __construct(Request $request)
    {
        $this->slug = $request->attributes->get('slug');

        $this->title = $request->request->get('title');
        $this->channelCode = $request->request->get('channelCode');
        $this->rating = $request->request->get('rating');
        $this->comment = $request->request->get('comment');
        $this->email = $request->request->get('email');
    }

    public function getCommand(): AddProductReviewBySlug
    {
        return new AddProductReviewBySlug($this->slug, $this->channelCode, $this->title, $this->rating, $this->comment, $this->email);
    }
}
