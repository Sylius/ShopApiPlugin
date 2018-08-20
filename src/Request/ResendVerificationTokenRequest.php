<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Request;

use Sylius\SyliusShopApiPlugin\Command\SendVerificationToken;
use Symfony\Component\HttpFoundation\Request;

final class ResendVerificationTokenRequest
{
    /**
     * @var string
     */
    private $email;

    public function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
    }

    public function getCommand(): SendVerificationToken
    {
        return new SendVerificationToken($this->email);
    }
}
