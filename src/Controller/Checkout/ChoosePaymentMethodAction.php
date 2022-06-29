<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class ChoosePaymentMethodAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var CommandProviderInterface */
    private $choosePaymentMethodCommandProvider;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        CommandProviderInterface $choosePaymentMethodCommandProvider,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->choosePaymentMethodCommandProvider = $choosePaymentMethodCommandProvider;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
    }

    public function __invoke(Request $request): Response
    {
        $validationResults = $this->choosePaymentMethodCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST,
            ));
        }

        $this->bus->dispatch($this->choosePaymentMethodCommandProvider->getCommand($request));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
