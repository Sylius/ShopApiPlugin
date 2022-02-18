<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Customer;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class RegisterCustomer implements CommandInterface
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $plainPassword;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $channelCode;

    /** @var bool */
    protected $subscribedToNewsletter;

    /** @var string */
    protected $phoneNumber;

    public function __construct(
        string $email,
        string $plainPassword,
        string $firstName,
        string $lastName,
        string $channelCode,
        ?bool $subscribedToNewsletter,
        ?string $phoneNumber
    ) {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->channelCode = $channelCode;
        $this->subscribedToNewsletter = $subscribedToNewsletter === null ? false : $subscribedToNewsletter;
        $this->phoneNumber = $phoneNumber;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function plainPassword(): string
    {
        return $this->plainPassword;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }

    public function subscribedToNewsletter(): ?bool
    {
        return $this->subscribedToNewsletter;
    }

    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }
}
