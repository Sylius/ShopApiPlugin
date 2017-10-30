<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\Command\CreateAddress;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Request;

final class CreateAddressRequest
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $provinceCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $postcode;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * CreateAddressRequest constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->firstName = $request->request->get('firstName');
        $this->lastName = $request->request->get('lastName');
        $this->city = $request->request->get('city');
        $this->postcode = $request->request->get('postcode');
        $this->street = $request->request->get('street');
        $this->countryCode = $request->request->get('countryCode');
        $this->provinceCode = $request->request->get('provinceCode');
        $this->phoneNumber = $request->request->get('phoneNumber');
        $this->company = $request->request->get('company');

        $this->address = Address::createFromArray($request->request->all());
    }

    /**
     * @return CreateAddress
     */
    public function getCommand()
    {
        return new CreateAddress($this->address);
    }
}
