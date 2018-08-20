<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Request;

use Sylius\SyliusShopApiPlugin\Command\RegisterCustomer;
use Symfony\Component\HttpFoundation\Request;

final class RegisterCustomerRequest
{
    /** @var string */
    private $email;

    /** @var string */
    private $plainPassword;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $channelCode;

    public function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
        $this->plainPassword = $request->request->get('plainPassword');
        $this->firstName = $request->request->get('firstName');
        $this->lastName = $request->request->get('lastName');
        $this->channelCode = $request->request->get('channel');
    }

    public function getCommand(): RegisterCustomer
    {
        return new RegisterCustomer($this->email, $this->plainPassword, $this->firstName, $this->lastName, $this->channelCode);
    }
}
