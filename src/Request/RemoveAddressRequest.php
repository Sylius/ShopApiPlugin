<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\RemoveAddress;
use Symfony\Component\HttpFoundation\Request;

final class RemoveAddressRequest
{
    /**
     * @var mixed
     */
    private $id;

    public function __construct(Request $request)
    {
        $this->id = $request->attributes->get('id');
    }

    public function getCommand()
    {
        return new RemoveAddress($this->id);
    }
}
