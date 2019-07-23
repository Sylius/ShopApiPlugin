<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class UserCartRecalculationListener
{
    /** @var CartContextInterface */
    private $cartContext;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var ObjectManager */
    private $cartManager;

    public function __construct(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        ObjectManager $cartManager
    ) {
        $this->cartContext = $cartContext;
        $this->orderProcessor = $orderProcessor;
        $this->cartManager = $cartManager;
    }

    public function recalculateCartWhileLogin(): void
    {
        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            return;
        }

        Assert::isInstanceOf($cart, OrderInterface::class);

        $this->orderProcessor->process($cart);

        $this->cartManager->flush();
    }
}
