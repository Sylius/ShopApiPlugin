<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Sylius\ShopApiPlugin\Command\Cart\PutOptionBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class PutOptionBasedConfigurableItemToCartRequest implements RequestInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $productCode;

    /** @var array|null */
    protected $options;

    /** @var int */
    protected $quantity;

    protected function __construct(?string $token, ?string $productCode, ?array $options, ?int $quantity)
    {
        $this->token = $token;
        $this->productCode = $productCode;
        $this->options = $options;
        $this->quantity = $quantity;
    }

    public function getProductCode(): string
    {
        return $this->productCode ?? '';
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public static function fromArray(array $item): self
    {
        return new self(
            $item['token'] ?? null,
            $item['productCode'] ?? null,
            $item['options'] ?? null,
            $item['quantity'] ?? null,
        );
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self(
            $request->attributes->get('token'),
            $request->request->get('productCode'),
            $request->request->get('options'),
            $request->request->getInt('quantity', 1),
        );
    }

    public function getCommand(): CommandInterface
    {
        return new PutOptionBasedConfigurableItemToCart(
            $this->token,
            $this->productCode,
            $this->options,
            $this->quantity,
        );
    }
}
