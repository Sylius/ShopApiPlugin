<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\VerifyAccount;
use Symfony\Component\HttpFoundation\Request;

class VerifyAccountRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    public function populateData(Request $request): void
    {
        $this->token = $request->request->get('token');
    }

    public function getCommand(): object
    {
        return new VerifyAccount($this->token);
    }
}
