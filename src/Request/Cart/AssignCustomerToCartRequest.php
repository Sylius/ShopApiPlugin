<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class AssignCustomerToCartRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $email;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->email = $request->request->get('email');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new AssignCustomerToCart($this->token, $this->email);
    }
}
