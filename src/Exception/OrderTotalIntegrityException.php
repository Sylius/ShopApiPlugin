<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Exception;

use Throwable;

final class OrderTotalIntegrityException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Your order total has been changed, check your order information and confirm it again.');
    }
}
