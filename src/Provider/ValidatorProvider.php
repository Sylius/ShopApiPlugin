<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Provider;

use FOS\RestBundle\View\View;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationViewProvider implements ValidatorProviderInterface
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    public function __construct(
        ValidatorInterface $validator,
        ValidatorErrorViewFactoryInterface $validationErrorViewFactory
    ) {
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
    }

    public function provide($request): ?View
    {
        $results = $this->validator->validate($request);
        if (0 < count($results)) {
            return View::create($this->validationErrorViewFactory->create($results), Response::HTTP_BAD_REQUEST);
        }

        return null;
    }
}
