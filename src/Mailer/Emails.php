<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
