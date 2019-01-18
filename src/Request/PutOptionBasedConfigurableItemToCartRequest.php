<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PutOptionBasedConfigurableItemToCart;
use Symfony\Component\HttpFoundation\Request;

class PutOptionBasedConfigurableItemToCartRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $productCode;

    /** @var array|null */
    protected $options;

    /** @var int */
    protected $quantity;

    public static function fromArray(array $item): self
    {
        $commandRequest = new self();
        $commandRequest->token = $item['token'] ?? null;
        $commandRequest->productCode = $item['productCode'] ?? null;
        $commandRequest->options = $item['options'] ?? null;
        $commandRequest->quantity = $item['quantity'] ?? null;

        return $commandRequest;
    }

    public function populateData(Request $request): void
    {
        $this->token = $request->attributes->get('token');
        $this->productCode = $request->request->get('productCode');
        $this->options = $request->request->get('options');
        $this->quantity = $request->request->getInt('quantity', 1);
    }

    public function getCommand(): object
    {
        return new PutOptionBasedConfigurableItemToCart($this->token, $this->productCode, $this->options, $this->quantity);
    }
}
