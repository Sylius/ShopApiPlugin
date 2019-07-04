<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Traits;


use Sylius\Component\Core\Model\OrderInterface;

trait CustomerGuestAuthenticationTrait
{
    /**
     * @var OrderInterface
     *
     * The order authorized to view as guest
     */
    private $authorizedOrder;

    public function getAuthorizedOrder(): OrderInterface
    {
        return $this->authorizedOrder;
    }

    public function setAuthorizedOrder(OrderInterface $authorizedOrder): void
    {
        $this->authorizedOrder = $authorizedOrder;
    }
}
