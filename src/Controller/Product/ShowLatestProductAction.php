<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\ViewRepository\Product\ProductLatestViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowLatestProductAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ProductLatestViewRepositoryInterface */
    private $productLatestQuery;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ProductLatestViewRepositoryInterface $productLatestQuery,
        ChannelContextInterface $channelContext
    ) {
        $this->viewHandler = $viewHandler;
        $this->productLatestQuery = $productLatestQuery;
        $this->channelContext = $channelContext;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $channel = $this->channelContext->getChannel();

            return $this->viewHandler->handle(View::create($this->productLatestQuery->getLatestProducts(
                $channel->getCode(),
                $request->query->get('locale'),
                new PaginatorDetails($request->attributes->get('_route'), $request->query->all())
            ), Response::HTTP_OK));
        } catch (ChannelNotFoundException $exception) {
            throw new NotFoundHttpException('Channel has not been found.');
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
