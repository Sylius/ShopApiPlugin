<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\ShopApiPlugin\Command\DropCart;
use Sylius\ShopApiPlugin\Validator\Constraints\CartExists;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DropCartCommandProvider implements CommandProviderInterface
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Request $request): ConstraintViolationListInterface
    {
        $constraint = new Assert\Collection([
            'token' => [
                new Assert\NotBlank(),
                new CartExists(),
            ],
        ]);

        return $this->validator->validate($this->provideRawData($request), $constraint);
    }

    public function getCommand(Request $request): object
    {
        $violationList = $this->validate($request);

        if (0 === count($violationList)) {
            $rawData = $this->provideRawData($request);

            return new DropCart($rawData['token']);
        }

        throw new \InvalidArgumentException('Command cannot be created');
    }

    private function provideRawData(Request $request): array
    {
        return ['token' => $request->attributes->get('token')];
    }
}
