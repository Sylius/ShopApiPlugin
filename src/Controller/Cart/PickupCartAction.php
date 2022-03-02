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

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

final class PickupCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var ChannelBasedCommandProviderInterface */
    private $pickupCartCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        ChannelContextInterface $channelContext,
        ChannelBasedCommandProviderInterface $pickupCartCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->channelContext = $channelContext;
        $this->pickupCartCommandProvider = $pickupCartCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $validationResults = $this->pickupCartCommandProvider->validate($request, $channel);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        /** @var PickupCart $pickupCartCommand */
        $pickupCartCommand = $this->pickupCartCommandProvider->getCommand($request, $channel);
        $this->bus->dispatch($pickupCartCommand);

        try {
            return $this->viewHandler->handle(View::create(
                $this->cartQuery->getOneByToken($pickupCartCommand->orderToken()),
                Response::HTTP_CREATED
            ));
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
