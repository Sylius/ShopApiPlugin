<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Country\Province;

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\ShopApiPlugin\View\Country\Province\ProvinceView;

interface ProvinceViewFactoryInterface
{
    public function create(ProvinceInterface $province): ProvinceView;
}
