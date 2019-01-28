<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\ShopApiPlugin\Command\DropCart;
use Sylius\ShopApiPlugin\Validator\Constraints\CartExists;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DropCartCommandProvider implements CommandProviderInterface
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function provide(Request $request): object
    {
        $rawData = [
            'token' => $request->attributes->get('token'),
        ];

        $violationList = $this->validator->validate($rawData, new Assert\Collection([
            'token' => [
                new Assert\NotBlank(),
                new CartExists(),
            ],
        ]));

        if (count($violationList) > 0) {
            throw ValidationFailedException::fromSymfonyConstraintValidationList($violationList);
        }

        return new DropCart($rawData['token']);
    }
}
