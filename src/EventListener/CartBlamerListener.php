<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class CartBlamerListener
{
    /** @var ObjectManager */
    private $cartManager;

    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var MessageBusInterface */
    private $bus;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(
        ObjectManager $cartManager,
        OrderRepositoryInterface $cartRepository,
        MessageBusInterface $bus,
        OrderProcessorInterface $orderProcessor,
        RequestStack $requestStack
    ) {
        $this->cartManager = $cartManager;
        $this->cartRepository = $cartRepository;
        $this->bus = $bus;
        $this->orderProcessor = $orderProcessor;
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

        $token = $request->request->get('token');

        $cart = $this->cartRepository->findOneBy(['tokenValue' => $token]);

        if (null === $cart) {
            return;
        }

        $this->bus->dispatch(
            new AssignCustomerToCart(
                $token,
                $user->getCustomer()->getEmail()
            )
        );

        $this->orderProcessor->process($cart);

        $this->cartManager->persist($cart);
        $this->cartManager->flush();
    }
}
