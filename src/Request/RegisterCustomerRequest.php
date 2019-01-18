<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\RegisterCustomer;
use Symfony\Component\HttpFoundation\Request;

class RegisterCustomerRequest implements CommandRequestInterface
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

    public function populateData(Request $request): void
    {
        $this->channelCode = $request->attributes->get('channelCode');

        $this->email = $request->request->get('email');
        $this->plainPassword = $request->request->get('plainPassword');
        $this->firstName = $request->request->get('firstName');
        $this->lastName = $request->request->get('lastName');
    }

    public function getCommand(): object
    {
        return new RegisterCustomer($this->email, $this->plainPassword, $this->firstName, $this->lastName, $this->channelCode);
    }
}
