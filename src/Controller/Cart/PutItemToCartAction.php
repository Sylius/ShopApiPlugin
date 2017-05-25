<?php

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Request\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\PutVariantBasedConfigurableItemToCartRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PutItemToCartAction
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
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given id does not exists');
        }

        $command = $this->provideCommandRequest($request);

        $this->bus->handle($command->getCommand());

        return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }

    /**
     * @param Request $request
     *
     * @return PutOptionBasedConfigurableItemToCartRequest|PutSimpleItemToCartRequest|PutVariantBasedConfigurableItemToCartRequest
     */
    private function provideCommandRequest(Request $request)
    {
        if (!$request->request->has('variantCode') && !$request->request->has('options')) {
            return new PutSimpleItemToCartRequest($request);
        }

        if ($request->request->has('variantCode') && !$request->request->has('options')) {
            return new PutVariantBasedConfigurableItemToCartRequest($request);
        }

        if (!$request->request->has('variantCode') && $request->request->has('options')) {
            return new PutOptionBasedConfigurableItemToCartRequest($request);
        }

        throw new NotFoundHttpException('Variant not found for given configuration');
    }
}
