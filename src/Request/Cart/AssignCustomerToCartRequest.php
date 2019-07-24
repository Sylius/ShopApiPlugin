<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\ShopUserBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class AssignCustomerToCartRequest implements ShopUserBasedRequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $email;

    private function __construct(Request $request, ?string $email)
    {
        $this->token = $request->attributes->get('token');
        $this->email = $request->request->get('email', $email);
    }

    public static function fromHttpRequestAndShopUser(Request $request, ?ShopUserInterface $user): ShopUserBasedRequestInterface
    {
        return new self($request, $user !== null ? $user->getEmail() : null);
    }

    public function getCommand(): CommandInterface
    {
        return new AssignCustomerToCart($this->token, $this->email);
    }
}
