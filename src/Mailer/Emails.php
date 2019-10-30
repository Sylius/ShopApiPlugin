<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Mailer;

final class Emails
{
    public const EMAIL_VERIFICATION_TOKEN = 'api_verification_token';

    public const EMAIL_RESET_PASSWORD_TOKEN = 'api_reset_password_token';

    public const EMAIL_ORDER_CONFIRMATION = 'api_order_confirmation';

    private function __construct()
    {
    }
}
