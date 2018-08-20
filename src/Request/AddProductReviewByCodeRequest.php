<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Request;

use Sylius\SyliusShopApiPlugin\Command\AddProductReviewByCode;
use Symfony\Component\HttpFoundation\Request;

final class AddProductReviewByCodeRequest
{
    /** @var string */
    private $code;

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
        $this->code = $request->attributes->get('code');

        $this->title = $request->request->get('title');
        $this->channelCode = $request->request->get('channelCode');
        $this->rating = $request->request->get('rating');
        $this->comment = $request->request->get('comment');
        $this->email = $request->request->get('email');
    }

    public function getCommand(): AddProductReviewByCode
    {
        return new AddProductReviewByCode($this->code, $this->channelCode, $this->title, $this->rating, $this->comment, $this->email);
    }
}
