<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Checkout;

use Sylius\ShopApiPlugin\Command\Cart\CompleteOrderWithCustomer;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class CompleteOrderWithCustomerRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $email;

    /** @var string|null */
    protected $notes;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->email = $request->request->get('email');
        $this->notes = $request->request->get('notes');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new CompleteOrderWithCustomer($this->token, $this->email, $this->notes);
    }
}
