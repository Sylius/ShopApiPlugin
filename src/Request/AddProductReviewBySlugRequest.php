<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\AddProductReviewBySlug;
use Symfony\Component\HttpFoundation\Request;

class AddProductReviewBySlugRequest implements CommandRequestInterface
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

    public function populateData(Request $request): void
    {
        $this->slug = $request->attributes->get('slug');
        $this->channelCode = $request->attributes->get('channelCode');

        $this->title = $request->request->get('title');
        $this->rating = $request->request->get('rating');
        $this->comment = $request->request->get('comment');
        $this->email = $request->request->get('email');
    }

    public function getCommand(): object
    {
        return new AddProductReviewBySlug($this->slug, $this->channelCode, $this->title, $this->rating, $this->comment, $this->email);
    }
}
