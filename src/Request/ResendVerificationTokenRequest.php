<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\SendVerificationToken;
use Symfony\Component\HttpFoundation\Request;

class ResendVerificationTokenRequest
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $channelCode;

    public function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
        $this->channelCode = $request->attributes->get('channelCode');
    }

    public function getCommand(): SendVerificationToken
    {
        return new SendVerificationToken($this->email, $this->channelCode);
    }
}
