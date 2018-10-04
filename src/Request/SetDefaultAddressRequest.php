<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Symfony\Component\HttpFoundation\Request;

final class SetDefaultAddressRequest implements CommandRequestInterface
{
    /** @var mixed */
    private $id;

    public function __construct(Request $request)
    {
        $this->id = $request->attributes->get('id');
    }
}
