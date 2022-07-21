<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;
use Webmozart\Assert\Assert;

final class AssignCustomerToCartHandler
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        OrderProcessorInterface $orderProcessor,
        CustomerProviderInterface $customerProvider,
    ) {
        $this->cartRepository = $cartRepository;
        $this->customerProvider = $customerProvider;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(AssignCustomerToCart $assignOrderToCustomer): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $assignOrderToCustomer->orderToken()]);

        Assert::notNull($cart, sprintf('Order with %s token has not been found.', $assignOrderToCustomer->orderToken()));

        $customer = $this->customerProvider->provide($assignOrderToCustomer->email());

        $cart->setCustomer($customer);

        $this->orderProcessor->process($cart);
    }
}
