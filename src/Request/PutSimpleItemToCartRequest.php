<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Request;

class PutSimpleItemToCartRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $productCode;

    /** @var int */
    protected $quantity;

    public static function fromArray(array $item): self
    {
        $commandRequest = new self();
        $commandRequest->token = $item['token'] ?? null;
        $commandRequest->productCode = $item['productCode'] ?? null;
        $commandRequest->quantity = $item['quantity'] ?? null;

        return $commandRequest;
    }

    public function populateData(Request $request): void
    {
        $this->token = $request->attributes->get('token');
        $this->productCode = $request->request->get('productCode');
        $this->quantity = $request->request->getInt('quantity', 1);
    }

    public function getCommand(): object
    {
        return new PutSimpleItemToCart($this->token, $this->productCode, $this->quantity);
    }
}
