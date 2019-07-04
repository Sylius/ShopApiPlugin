<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Traits;


use Ramsey\Uuid\Uuid;
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

    /*
     * The following methods are required by the UserInterface, which we need to be able to allow a guest login
     */

    /** {@inheritdoc} */
    public function getRoles()
    {
        return [];
    }

    /** {@inheritdoc} */
    public function getPassword()
    {
        return '';
    }

    /** {@inheritdoc} */
    public function getSalt()
    {
        return null;
    }

    /** {@inheritdoc} */
    public function getUsername()
    {
        // Generate a unique temporary username
        return sprintf('guest_%s_%s', str_replace('@', '(at)', $this->email), Uuid::uuid4()->toString());
    }

    /** {@inheritdoc} */
    public function eraseCredentials()
    {
    }
}
