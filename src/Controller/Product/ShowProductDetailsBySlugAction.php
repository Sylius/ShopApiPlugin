<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\ShopApiPlugin\ViewRepository\Product\ProductDetailsViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductDetailsBySlugAction
{
    /** @var ProductDetailsViewRepositoryInterface */
    private $productCatalog;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ProductDetailsViewRepositoryInterface $productCatalog,
        ViewHandlerInterface $viewHandler,
        ChannelContextInterface $channelContext
    ) {
        $this->productCatalog = $productCatalog;
        $this->viewHandler = $viewHandler;
        $this->channelContext = $channelContext;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $channel = $this->channelContext->getChannel();

            return $this->viewHandler->handle(View::create($this->productCatalog->findOneBySlug(
                $request->attributes->get('slug'),
                $channel->getCode(),
                $request->query->get('locale')
            ), Response::HTTP_OK));
        } catch (ChannelNotFoundException $exception) {
            throw new NotFoundHttpException('Channel has not been found.');
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
