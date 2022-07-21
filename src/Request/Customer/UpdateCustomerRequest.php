<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Customer;

use DateTimeImmutable;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\Customer\UpdateCustomer;
use Sylius\ShopApiPlugin\Request\ShopUserBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class UpdateCustomerRequest implements ShopUserBasedRequestInterface
{
    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string|null */
    protected $email;

    /** @var DateTimeImmutable|null */
    protected $birthday;

    /** @var string */
    protected $gender;

    /** @var string|null */
    protected $phoneNumber;

    /** @var bool */
    protected $subscribedToNewsletter;

    protected function __construct(Request $request, ShopUserInterface $user)
    {
        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();

        $this->email = $customer->getEmail();

        $this->firstName = $request->request->get('firstName');
        $this->lastName = $request->request->get('lastName');
        $this->birthday = $request->request->get('birthday');
        if ($this->birthday !== null) {
            $this->birthday = new DateTimeImmutable($this->birthday);
        }
        $this->gender = $request->request->get('gender');
        $this->phoneNumber = $request->request->get('phoneNumber');
        $this->subscribedToNewsletter = $request->request->getBoolean('subscribedToNewsletter') ?? false;
    }

    public static function fromHttpRequestAndShopUser(Request $request, ShopUserInterface $user): ShopUserBasedRequestInterface
    {
        return new self($request, $user);
    }

    public function getCommand(): CommandInterface
    {
        return new UpdateCustomer(
            $this->firstName,
            $this->lastName,
            $this->email,
            $this->birthday,
            $this->gender,
            $this->phoneNumber,
            $this->subscribedToNewsletter,
        );
    }
}
