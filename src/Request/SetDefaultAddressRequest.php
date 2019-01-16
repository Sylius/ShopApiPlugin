<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Symfony\Component\HttpFoundation\Request;

class SetDefaultAddressRequest
{
    /** @var mixed */
    protected $id;

    public function __construct(Request $request)
    {
        $this->id = $request->attributes->get('id');
    }
}
