<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use DateTimeInterface;
use Sylius\ShopApiPlugin\Command\UpdateCustomer;
use Symfony\Component\HttpFoundation\Request;

class UpdateCustomerRequest
{
    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string|null */
    protected $email;

    /** @var DateTimeInterface|null */
    protected $birthday;

    /** @var string */
    protected $gender;

    /** @var string|null */
    protected $phoneNumber;

    /** @var bool */
    protected $subscribedToNewsletter;

    public function __construct(Request $request)
    {
        $this->firstName = $request->request->get('firstName');
        $this->lastName = $request->request->get('lastName');
        $this->email = $request->request->get('email');
        $this->birthday = $request->request->get('birthday');
        $this->gender = $request->request->get('gender');
        $this->phoneNumber = $request->request->get('phoneNumber');
        $this->subscribedToNewsletter = $request->request->getBoolean('subscribedToNewsletter') ?? false;
    }

    public function getCommand(): UpdateCustomer
    {
        return new UpdateCustomer($this->firstName, $this->lastName, $this->email, $this->birthday, $this->gender, $this->phoneNumber, $this->subscribedToNewsletter);
    }
}
