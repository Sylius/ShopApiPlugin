<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\ViewRepository\Product\ProductReviewsViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShowProductReviewsByCodeAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ProductReviewsViewRepositoryInterface */
    private $productReviewsViewRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ProductReviewsViewRepositoryInterface $productReviewsViewRepository,
        ChannelContextInterface $channelContext,
    ) {
        $this->viewHandler = $viewHandler;
        $this->productReviewsViewRepository = $productReviewsViewRepository;
        $this->channelContext = $channelContext;
    }

    public function __invoke(Request $request): Response
    {
        $channel = $this->channelContext->getChannel();

        $page = $this->productReviewsViewRepository->getByProductCode(
            $request->attributes->get('code'),
            $channel->getCode(),
            new PaginatorDetails($request->attributes->get('_route'), $request->query->all()),
        );

        return $this->viewHandler->handle(View::create($page, Response::HTTP_OK));
    }
}
