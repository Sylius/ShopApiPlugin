<?php

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

    /** {@inheritdoc} */
    public function validate($token, Constraint $constraint)
    {
        if (null === $token || null === $this->channelRepository->findOneByCode($token)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
