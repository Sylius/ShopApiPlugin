<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class EstimateShippingCost
{
    /**
     * @var string
     */
    private $cartToken;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var null|string
     */
    private $provinceCode;

    /**
     * @var array
     */
    private $result = [];

    public function __construct(string $cartToken, string $countryCode, ?string $provinceCode=null)
    {
        $this->cartToken = $cartToken;
        $this->countryCode = $countryCode;
        $this->provinceCode = $provinceCode;
    }

    public function cartToken(): string
    {
        return $this->cartToken;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function provinceCode(): ?string
    {
        return $this->provinceCode;
    }

    /**
     * @param int    $value
     * @param string $currencyCode
     */
    public function setResult(int $value, string $currencyCode)
    {
        $this->result = [$value, $currencyCode];
    }

    /**
     *Returns the array of value, currency code
     *
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }
}
