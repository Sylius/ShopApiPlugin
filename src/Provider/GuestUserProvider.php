<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Provider;

use ReflectionClass;
use Sylius\ShopApiPlugin\Encoder\GuestOrderJWTEncoderInterface;
use Sylius\ShopApiPlugin\Traits\CustomerGuestAuthenticationInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Webmozart\Assert\Assert;

final class GuestUserProvider implements UserProviderInterface
{
    /** @var GuestOrderJWTEncoderInterface */
    private $encoder;

    public function __construct(GuestOrderJWTEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function loadUserByUsername($jwt): UserInterface
    {
        $order = $this->encoder->decode($jwt);

        /** @var CustomerGuestAuthenticationInterface $customer */
        $customer = $order->getCustomer();
        Assert::implementsInterface($customer, CustomerGuestAuthenticationInterface::class);

        $customer->setAuthorizedOrder($order);

        return $customer;
    }

    public function refreshUser(UserInterface $user)
    {
    }

    public function supportsClass($class): bool
    {
        return (new ReflectionClass($class))->implementsInterface(CustomerGuestAuthenticationInterface::class);
    }
}
