<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Factory\Cart\EstimatedShippingCostViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\Cart\EstimateShippingCostRequest;
use Sylius\ShopApiPlugin\Shipping\ShippingCostEstimatorInterface;
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

    /** @var EstimatedShippingCostViewFactoryInterface */
    private $estimatedShippingCostViewFactory;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ShippingCostEstimatorInterface $shippingCostEstimator,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        EstimatedShippingCostViewFactoryInterface $estimatedShippingCostViewFactory,
    ) {
        $this->viewHandler = $viewHandler;
        $this->shippingCostEstimator = $shippingCostEstimator;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->estimatedShippingCostViewFactory = $estimatedShippingCostViewFactory;
    }

    public function __invoke(Request $request): Response
    {
        $estimateShippingCostRequest = new EstimateShippingCostRequest($request);

        $validationResults = $this->validator->validate($estimateShippingCostRequest);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(
                View::create(
                    $this->validationErrorViewFactory->create($validationResults),
                    Response::HTTP_BAD_REQUEST,
                ),
            );
        }

        $shippingCost = $this->shippingCostEstimator->estimate(
            $estimateShippingCostRequest->cartToken(),
            $estimateShippingCostRequest->countryCode(),
            $estimateShippingCostRequest->provinceCode(),
        );

        return $this->viewHandler->handle(
            View::create($this->estimatedShippingCostViewFactory->create($shippingCost), Response::HTTP_OK),
        );
    }
}
