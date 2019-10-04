<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Customer;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class RegisterCustomer implements CommandInterface
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $plainPassword;

    /** @var string|null */
    protected $firstName = '';

    /** @var string|null */
    protected $lastName = '';

    /** @var string */
    protected $channelCode;

    public function __construct(string $email, string $plainPassword,  $firstName = '',  $lastName = '', string $channelCode = 'default')
    {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->channelCode = $channelCode;
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
        $firstName = '';
        if($this->firstName){
            $firstName = $this->firstName;
        }
        return $firstName;
    }

    public function lastName(): string
    {
        $lastName = '';
        if($this->lastName){
            $lastName = $this->lastName;
        }
        return $lastName;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }
}
