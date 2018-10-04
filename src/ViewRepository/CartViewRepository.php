<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Webmozart\Assert\Assert;

final class CartViewRepository implements CartViewRepositoryInterface
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var CartViewFactoryInterface */
    private $cartViewFactory;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        CustomerRepositoryInterface $customerRepository,
        CartViewFactoryInterface $cartViewFactory
    ) {
        $this->cartRepository = $cartRepository;
        $this->customerRepository = $customerRepository;
        $this->cartViewFactory = $cartViewFactory;
    }

    public function getOneByToken(string $orderToken): CartSummaryView
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $orderToken]);

        Assert::notNull($cart, 'Cart with given id does not exists');

        return $this->cartViewFactory->create($cart, $cart->getLocaleCode());
    }

    public function getCompletedByCustomerEmail(string $customerEmail): array
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        Assert::notNull($customer);

        $cartViews = [];
        /** @var OrderInterface $cart */
        foreach ($this->cartRepository->findBy(['customer' => $customer]) as $cart) {
            if ($cart->getCheckoutState() === OrderCheckoutStates::STATE_COMPLETED) {
                $cartViews[] = $this->cartViewFactory->create($cart, $cart->getLocaleCode());
            }
        }

        return $cartViews;
    }
}
