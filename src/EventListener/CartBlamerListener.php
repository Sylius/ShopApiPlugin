<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class CartBlamerListener
{
    /** @var ObjectManager */
    private $cartManager;

    /** @var CartContextInterface */
    private $cartContext;

    /** @var OrderRepositoryInterface */
    private $cartRepository;
    /** @var RequestStack */
    private $requestStack;

    public function __construct(
        ObjectManager $cartManager,
        CartContextInterface $cartContext,
        OrderRepositoryInterface $cartRepository,
        RequestStack $requestStack
    ) {
        $this->cartManager = $cartManager;
        $this->cartContext = $cartContext;
        $this->cartRepository = $cartRepository;
        $this->requestStack = $requestStack;
    }

    public function onJwtLogin(JWTCreatedEvent $interactiveLoginEvent): void
    {
        $user = $interactiveLoginEvent->getUser();
        $request = $this->requestStack->getCurrentRequest();

        Assert::notNull($request);

        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $cart = $this->getCart($request->request->get('token'));

        if (null === $cart) {
            return;
        }

        $cart->setCustomer($user->getCustomer());
        $this->cartManager->persist($cart);
        $this->cartManager->flush();
    }

    private function getCart(?string $token): ?OrderInterface
    {
        if (null !== $token) {
            /** @var OrderInterface $cart */
            $cart = $this->cartRepository->findOneBy(['tokenValue' => $token]);

            return $cart;
        }

        try {
            /** @var OrderInterface $cart */
            $cart = $this->cartContext->getCart();

            return $cart;
        } catch (CartNotFoundException $exception) {
            return null;
        }
    }
}
