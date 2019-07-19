<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Customer;

use Sylius\ShopApiPlugin\Command\Customer\SendVerificationToken;
use Symfony\Component\HttpFoundation\Request;

class ResendVerificationTokenRequest
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $channelCode;

    public function __construct(Request $request, string $channelCode)
    {
        $this->email = $request->request->get('email');
        $this->channelCode = $channelCode;
    }

    public function getCommand(): SendVerificationToken
    {
        return new SendVerificationToken($this->email, $this->channelCode);
    }
}
