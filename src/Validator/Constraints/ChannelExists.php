<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ChannelExists extends Constraint
{
    /** @var string */
    public $message = 'sylius.shop_api.channel.not_exists';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'sylius_shop_api_channel_exists_validator';
    }
}
