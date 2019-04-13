<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Customer;

use Sylius\ShopApiPlugin\Command\Customer\VerifyAccount;
use Symfony\Component\HttpFoundation\Request;

class VerifyAccountRequest
{
    /** @var string */
    protected $token;

    public function __construct(Request $request)
    {
        $this->token = $request->query->get('token');
    }

    public function getCommand(): VerifyAccount
    {
        return new VerifyAccount($this->token);
    }
}
