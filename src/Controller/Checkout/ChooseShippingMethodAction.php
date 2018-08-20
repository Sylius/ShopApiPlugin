<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChooseShippingMethodAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CommandBus */
    private $bus;

    public function __construct(ViewHandlerInterface $viewHandler, CommandBus $bus)
    {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
    }

    public function __invoke(Request $request)
    {
        $this->bus->handle(new ChooseShippingMethod(
            $request->attributes->get('token'),
            $request->attributes->get('shippingId'),
            $request->request->get('method')
        ));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
