<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ChannelExistsValidator extends ConstraintValidator
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /** @inheritdoc */
    public function validate(mixed $token, Constraint $constraint): void
    {
        if (null === $token || null === $this->channelRepository->findOneByCode($token)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
