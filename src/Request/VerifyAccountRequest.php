<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\VerifyAccount;
use Symfony\Component\HttpFoundation\Request;

final class VerifyAccountRequest implements CommandRequestInterface
{
    /** @var string */
    private $token;

    public function __construct(Request $request)
    {
        $this->token = $request->request->get('token');
    }

    public function getCommand(): CommandInterface
    {
        return new VerifyAccount($this->token);
    }
}
