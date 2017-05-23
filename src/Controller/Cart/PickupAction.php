<?php

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Request\PickupCartRequest;
use Sylius\ShopApiPlugin\View\ValidationErrorView;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PickupAction extends Controller
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
     * @param ViewHandlerInterface $viewHandler
     * @param CommandBus $bus
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $pickupRequest = new PickupCartRequest($request);

        $validationResults = $this->validator->validate($pickupRequest);

        if (0 === count($validationResults)) {
            $this->bus->handle($pickupRequest->getCommand());

            return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
        }

        $errorMessage = new ValidationErrorView();
        $errorMessage->code = 400;
        $errorMessage->message = 'Validation failed';
        /** @var ConstraintViolationInterface $result */
        foreach ($validationResults as $result) {
            $errorMessage->errors[$result->getPropertyPath()][] = $result->getMessage();
        }

        return $this->viewHandler->handle(View::create($errorMessage, Response::HTTP_BAD_REQUEST));
    }

}
