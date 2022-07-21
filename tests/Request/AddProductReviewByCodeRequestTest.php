<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\ShopApiPlugin\Command\Product\AddProductReviewByCode;
use Sylius\ShopApiPlugin\Request\Product\AddProductReviewByCodeRequest;
use Symfony\Component\HttpFoundation\Request;

final class AddProductReviewByCodeRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_review_with_author()
    {
        $channel = new Channel();
        $channel->setCode('WEB_GB');

        $addReviewRequest = AddProductReviewByCodeRequest::fromHttpRequestAndChannel(
            new Request(
                [],
                [
                    'title' => 'Awesome beer',
                    'rating' => 5,
                    'comment' => 'I love this beer',
                    'email' => 'pale.ale@brewery.com',
                ],
                [
                    'code' => 'PALE_ALE_CODE',
                ],
            ),
            $channel,
        );

        $this->assertEquals($addReviewRequest->getCommand(), new AddProductReviewByCode(
            'PALE_ALE_CODE',
            'WEB_GB',
            'Awesome beer',
            5,
            'I love this beer',
            'pale.ale@brewery.com',
        ));
    }
}
