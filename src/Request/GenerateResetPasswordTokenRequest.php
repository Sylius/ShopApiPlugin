<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\GenerateResetPasswordToken;
use Symfony\Component\HttpFoundation\Request;

class GenerateResetPasswordTokenRequest implements CommandRequestInterface
{
    /** @var string */
    protected $email;

    public function populateData(Request $request): void
    {
        $this->email = $request->request->get('email');
    }

    public function getCommand(): object
    {
        return new GenerateResetPasswordToken($this->email);
    }
}
