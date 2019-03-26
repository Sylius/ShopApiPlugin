<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\Cart\EstimateShippingCostRequest;
use Sylius\ShopApiPlugin\Shipping\ShippingCostEstimatorInterface;
use Sylius\ShopApiPlugin\View\EstimatedShippingCostView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EstimateShippingCostAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ShippingCostEstimatorInterface */
    private $shippingCostEstimator;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ShippingCostEstimatorInterface $shippingCostEstimator,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        PriceViewFactoryInterface $priceViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->shippingCostEstimator = $shippingCostEstimator;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->priceViewFactory = $priceViewFactory;
    }

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

        $shippingCost = $this->shippingCostEstimator->estimate(
            $estimateShippingCostRequest->cartToken(),
            $estimateShippingCostRequest->countryCode(),
            $estimateShippingCostRequest->provinceCode()
        );

        $estimatedShippingCostView = new EstimatedShippingCostView();
        $estimatedShippingCostView->price = $this->priceViewFactory->create($shippingCost->price(), $shippingCost->currency());

        return $this->viewHandler->handle(View::create($estimatedShippingCostView, Response::HTTP_OK));
    }
}
