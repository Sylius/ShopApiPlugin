<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class InteractiveLoginListener
{
    /**
     * @var ObjectManager
     */
    private $cartManager;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @param ObjectManager $cartManager
     * @param CartContextInterface $cartContext
     */
    public function __construct(ObjectManager $cartManager, CartContextInterface $cartContext)
    {
        $this->cartManager = $cartManager;
        $this->cartContext = $cartContext;
    }

    /**
     * @param InteractiveLoginEvent $interactiveLoginEvent
     */
    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent): void
    {
        // Skip if it's not a shop API request.
        if (!preg_match('/^shop_api_/', $interactiveLoginEvent->getRequest()->attributes->get('_route'))) {
            return;
        }

        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $cart = $this->getCart();
        if (null === $cart) {
            return;
        }

        if (null === $cart->getTokenValue()) {
            // Generate a hash
            $tokenValue = md5($user->getId() . $cart->getId() . time());

            $cart->setTokenValue($tokenValue);
            $this->cartManager->persist($cart);
            $this->cartManager->flush();
        }

        $interactiveLoginEvent->getRequest()->attributes->set('token', $cart->getTokenValue());
    }

    /**
     * @return OrderInterface|null
     *
     * @throws UnexpectedTypeException
     */
    private function getCart(): ?OrderInterface
    {
        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            return null;
        }

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        return $cart;
    }
}
