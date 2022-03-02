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

namespace Sylius\ShopApiPlugin\Request\Checkout;

use Sylius\ShopApiPlugin\Command\Cart\CompleteOrder;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class CompleteOrderRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $notes;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->notes = $request->request->get('notes');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new CompleteOrder($this->token, $this->notes);
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
