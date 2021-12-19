<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Country;

class CountryView
{
    /**
     * Country code ISO 3166-1 alpha-2.
     * @var string|null
     */
    public $code;

    /** @var string */
    public $name;

    /** @var array */
    public $provinces;
}
