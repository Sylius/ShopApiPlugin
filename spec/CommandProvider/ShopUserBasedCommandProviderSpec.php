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
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\CommandProvider\ShopUserBasedCommandProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Sylius\ShopApiPlugin\Mocks\TestShopUserBasedRequest;

final class ShopUserBasedCommandProviderSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator): void
    {
        $this->beConstructedWith(TestShopUserBasedRequest::class, $validator);
    }

    function it_implements_shop_user_command_provider_interface(): void
    {
        $this->shouldHaveType(ShopUserBasedCommandProviderInterface::class);
    }

    function it_validates_request(
        ValidatorInterface $validator,
        Request $httpRequest,
        ConstraintViolationListInterface $constraintViolationList,
        ShopUserInterface $user,
    ): void {
        $httpRequest->attributes = new ParameterBag(['token' => 'sample_cart_token']);
        $user->getEmail()->willReturn('example@example.com');

        $validator
            ->validate(TestShopUserBasedRequest::fromHttpRequestAndShopUser(
                $httpRequest->getWrappedObject(),
                $user->getWrappedObject(),
            ), null, null)->willReturn($constraintViolationList)
        ;

        $this->validate($httpRequest, $user)->shouldReturn($constraintViolationList);
    }
}
