<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\SendVerificationToken;
use Symfony\Component\HttpFoundation\Request;

final class ResendVerificationTokenRequest implements CommandRequestInterface
{
    /** @var string */
    private $email;

    public function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
    }

    public function getCommand(): CommandInterface
    {
        return new SendVerificationToken($this->email);
    }
}
