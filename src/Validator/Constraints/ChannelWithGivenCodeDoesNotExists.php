<?php

namespace Sylius\ShopApiPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ChannelWithGivenCodeDoesNotExists extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.shop_api.pickup_cart_request.channel.not_exists';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_shop_api_channel_with_given_code_does_not_exists_validator';
    }
}
