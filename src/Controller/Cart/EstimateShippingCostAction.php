<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Factory\PriceViewFactory;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\EstimateShippingCostRequest;
use Sylius\ShopApiPlugin\View\EstimatedShippingCostView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EstimateShippingCostAction
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ValidationErrorViewFactoryInterface
     */
    private $validationErrorViewFactory;

    /**
     * @var PriceViewFactory
     */
    private $priceViewFactory;

    /**
     * @param ViewHandlerInterface                $viewHandler
     * @param CommandBus                          $bus
     * @param ValidatorInterface                  $validator
     * @param ValidationErrorViewFactoryInterface $validationErrorViewFactory
     * @param PriceViewFactory                    $priceViewFactory
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        PriceViewFactory $priceViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->priceViewFactory = $priceViewFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $estimateShippingCostRequest = new EstimateShippingCostRequest($request);

        $validationResults = $this->validator->validate($estimateShippingCostRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(
                View::create(
                    $this->validationErrorViewFactory->create($validationResults),
                    Response::HTTP_BAD_REQUEST
                )
            );
        }

        $command = $estimateShippingCostRequest->getCommand();
        $this->bus->handle($command);

        $estimatedShippingCostView = new EstimatedShippingCostView();
        $estimatedShippingCostView->price = $this->priceViewFactory->create(...$command->getResult());

        return $this->viewHandler->handle(View::create($estimatedShippingCostView, Response::HTTP_OK));
    }
}