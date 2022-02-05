<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Symfony\Component\HttpFoundation\Request;

class EstimateShippingCostRequest
{
    /** @var string */
    protected $cartToken;

    /** @var string */
    protected $countryCode;

    /** @var string */
    protected $provinceCode;

    public function __construct(Request $request)
    {
        $this->cartToken = $request->attributes->get('token');
        $this->countryCode = $request->query->get('countryCode');
        $this->provinceCode = $request->query->get('provinceCode');
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
}
