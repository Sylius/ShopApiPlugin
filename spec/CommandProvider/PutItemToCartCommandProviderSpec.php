<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\CommandProvider;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Request\Cart\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutVariantBasedConfigurableItemToCartRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PutItemToCartCommandProviderSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator): void
    {
        $this->beConstructedWith($validator);
    }

    function it_implements_command_provider_interface(): void
    {
        $this->shouldHaveType(CommandProviderInterface::class);
    }

    function it_validates_put_simple_item_to_cart_request(
        ValidatorInterface $validator,
        Request $httpRequest,
        ConstraintViolationListInterface $constraintViolationList,
    ): void {
        $httpRequest->attributes = new ParameterBag(['token' => 'ORDERTOKEN']);
        $httpRequest->request = new ParameterBag([
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'quantity' => 4,
        ]);

        $validator
            ->validate(PutSimpleItemToCartRequest::fromHttpRequest($httpRequest->getWrappedObject()))
            ->willReturn($constraintViolationList)
        ;

        $this->validate($httpRequest)->shouldReturn($constraintViolationList);
    }

    function it_validates_put_variant_based_configurable_item_to_cart_request(
        ValidatorInterface $validator,
        Request $httpRequest,
        ConstraintViolationListInterface $constraintViolationList,
    ): void {
        $httpRequest->attributes = new ParameterBag(['token' => 'ORDERTOKEN']);
        $httpRequest->request = new ParameterBag([
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'variantCode' => 'LARGE_HACKTOBERFEST_TSHIRT_CODE',
            'quantity' => 4,
        ]);

        $validator
            ->validate(PutVariantBasedConfigurableItemToCartRequest::fromHttpRequest($httpRequest->getWrappedObject()))
            ->willReturn($constraintViolationList)
        ;

        $this->validate($httpRequest)->shouldReturn($constraintViolationList);
    }

    function it_validates_put_option_based_configurable_item_to_cart_request(
        ValidatorInterface $validator,
        Request $httpRequest,
        ConstraintViolationListInterface $constraintViolationList,
    ): void {
        $httpRequest->attributes = new ParameterBag(['token' => 'ORDERTOKEN']);
        $httpRequest->request = new ParameterBag([
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'options' => ['LARGE_CODE'],
            'quantity' => 4,
        ]);

        $validator
            ->validate(PutOptionBasedConfigurableItemToCartRequest::fromHttpRequest($httpRequest->getWrappedObject()))
            ->willReturn($constraintViolationList)
        ;

        $this->validate($httpRequest)->shouldReturn($constraintViolationList);
    }
}
