<?php

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CreateAddress;
use Symfony\Component\HttpFoundation\Request;

class CreateAddressRequest
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
     * CreateAddressRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->firstName = $request->request->get('firstName');
        $this->lastName = $request->request->get('lastName');
        $this->company = $request->request->get('company');
        $this->street = $request->request->get('street');
        $this->countryCode = $request->request->get('countryCode');
        $this->provinceCode = $request->request->get('provinceCode');
        $this->city = $request->request->get('city');
        $this->postcode = $request->request->get('postcode');
        $this->phoneNumber = $request->request->get('phoneNumber');
    }

    /**
     * @return CreateAddress
     */
    public function getCommand()
    {
        return new CreateAddress(
            $this->firstName,
            $this->lastName,
            $this->company,
            $this->street,
            $this->countryCode,
            $this->provinceCode,
            $this->city,
            $this->postcode,
            $this->phoneNumber
        );
    }

}