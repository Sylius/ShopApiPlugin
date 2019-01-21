<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddProductReviewByCode;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Parser\CommandRequestParserInterface;
use Sylius\ShopApiPlugin\Request\AddProductReviewByCodeRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AddReviewByCodeAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CommandBus */
    private $bus;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CommandRequestParserInterface */
    private $commandRequestParser;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CommandRequestParserInterface $commandRequestParser
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->commandRequestParser = $commandRequestParser;
    }

    public function __invoke(Request $request): Response
    {
        $addReviewRequest = $this->commandRequestParser->parse($request, AddProductReviewByCode::class);

        $validationResults = $this->validator->validate($addReviewRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        $this->bus->handle($addReviewRequest->getCommand());

        return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }
}
