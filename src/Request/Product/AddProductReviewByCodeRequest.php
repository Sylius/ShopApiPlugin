<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\Product\AddProductReviewByCode;
use Sylius\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class AddProductReviewByCodeRequest implements ChannelBasedRequestInterface
{
    /** @var string */
    protected $code;

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

    protected function __construct(Request $request, string $channelCode)
    {
        $this->code = $request->attributes->get('code');
        $this->title = $request->request->get('title');
        $this->rating = $request->request->get('rating');
        $this->comment = $request->request->get('comment');
        $this->email = $request->request->get('email');

        $this->channelCode = $channelCode;
    }

    public static function fromHttpRequestAndChannel(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        return new self($request, $channel->getCode());
    }

    public function getCommand(): CommandInterface
    {
        return new AddProductReviewByCode(
            $this->code,
            $this->channelCode,
            $this->title,
            $this->rating,
            $this->comment,
            $this->email
        );
    }
}
