<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;

final class CartBlamerListener
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var MessageBusInterface */
    private $bus;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        MessageBusInterface $bus,
        RequestStack $requestStack,
    ) {
        $this->cartRepository = $cartRepository;
        $this->bus = $bus;
        $this->requestStack = $requestStack;
    }

    public function onJwtLogin(JWTCreatedEvent $interactiveLoginEvent): void
    {
        $request = $this->requestStack->getCurrentRequest();
        // If there is no request then it was a console login, where there is no user to be assigned a cart
        if ($request === null) {
            return;
        }

        $user = $interactiveLoginEvent->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $token = $request->request->get('token');

        if (!$token) {
            return;
        }

        $cart = $this->cartRepository->findOneBy(['tokenValue' => $token]);

        if (null === $cart) {
            return;
        }

        $this->bus->dispatch(new AssignCustomerToCart($token, $user->getCustomer()->getEmail()));
    }
}
