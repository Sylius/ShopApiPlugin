<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\CoreBundle\EventListener\UserCartRecalculationListener as CoreListener;
use Symfony\Component\EventDispatcher\Event;

class UserCartRecalculationListener
{
    /** @var \Sylius\Bundle\CoreBundle\EventListener\UserCartRecalculationListener */
    private $recalculationListener;

    public function __construct(CoreListener $recalculationListener)
    {
        $this->recalculationListener = $recalculationListener;
    }

    public function recalculateCartWhileLogin(Event $event): void {
        $this->recalculationListener->recalculateCartWhileLogin($event);
    }

    public function onJwtLogin(JWTCreatedEvent $event): void
    {
        $this->recalculationListener->recalculateCartWhileLogin(new UserEvent($event->getUser()));
    }
}
