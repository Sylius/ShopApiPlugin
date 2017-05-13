<?php

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class PickupAction extends Controller
{
    /**
     * @var OrderRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @param OrderRepositoryInterface $cartRepository
     * @param ViewHandlerInterface $viewHandler
     * @param CommandBus $bus
     */
    public function __construct(OrderRepositoryInterface $cartRepository, ViewHandlerInterface $viewHandler, CommandBus $bus)
    {
        $this->cartRepository = $cartRepository;
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        if (null !== $this->cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')])) {
            throw new BadRequestHttpException('Cart with given token already exists');
        }

        $this->bus->handle(new PickupCart($request->attributes->get('token'), $request->request->get('channel')));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }

}
