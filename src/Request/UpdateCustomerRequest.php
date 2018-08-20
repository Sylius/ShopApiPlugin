<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use DateTimeInterface;
use Sylius\ShopApiPlugin\Command\UpdateCustomer;
use Symfony\Component\HttpFoundation\Request;

final class UpdateCustomerRequest
{
    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string|null */
    private $email;

    /** @var DateTimeInterface|null */
    private $birthday;

    /** @var string */
    private $gender;

    /** @var string|null */
    private $phoneNumber;

    /** @var bool */
    private $subscribedToNewsletter;

    public function __construct(Request $request)
    {
        $this->firstName = $request->request->get('firstName');
        $this->lastName = $request->request->get('lastName');
        $this->email = $request->request->get('email');
        $this->birthday = $request->request->get('birthday');
        $this->gender = $request->request->get('gender');
        $this->phoneNumber = $request->request->get('phoneNumber');
        $this->subscribedToNewsletter = (bool) $request->request->get('subscribedToNewsletter') ?? false;
    }

    public function getCommand()
    {
        return new UpdateCustomer($this->firstName, $this->lastName, $this->email, $this->birthday, $this->gender, $this->phoneNumber, $this->subscribedToNewsletter);
    }
}
