<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Sylius\Bundle\CoreBundle\EventListener\UserCartRecalculationListener as CoreListener;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserCartRecalculationListener
{
    /** @var CoreListener */
    private $recalculationListener;

    public function __construct(CoreListener $recalculationListener)
    {
        $this->recalculationListener = $recalculationListener;
    }

    public function recalculateCartWhileLogin(InteractiveLoginEvent $event): void
    {
        $this->recalculationListener->recalculateCartWhileLogin($event);
    }

    public function onJwtLogin(JWTCreatedEvent $event): void
    {
        /** @var UserInterface $user */
        $user = $event->getUser();
        $this->recalculationListener->recalculateCartWhileLogin(new UserEvent($user));
    }
}
