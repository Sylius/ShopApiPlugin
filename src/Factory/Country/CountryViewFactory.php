<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Country;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\ShopApiPlugin\View\Country\CountryView;

final class CountryViewFactory implements CountryViewFactoryInterface
{
    /** @var string */
    private $countryViewClass;

    public function __construct(string $countryViewClass)
    {
        $this->countryViewClass = $countryViewClass;
    }

    public function create(CountryInterface $country, array $provinces, string $localeCode): CountryView
    {
        /** @var CountryView $countryView */
        $countryView = new $this->countryViewClass();

        $countryView->code = $country->getCode();
        $countryView->name = $country->getName($localeCode);

        if ($provinces !== []) {
            $countryView->provinces = $provinces;
        }

        return $countryView;
    }
}
