<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Request;

use Sylius\SyliusShopApiPlugin\Command\VerifyAccount;
use Symfony\Component\HttpFoundation\Request;

final class VerifyAccountRequest
{
    /**
     * @var string
     */
    private $token;

    public function __construct(Request $request)
    {
        $this->token = $request->request->get('token');
    }

    public function getCommand(): VerifyAccount
    {
        return new VerifyAccount($this->token);
    }
}
