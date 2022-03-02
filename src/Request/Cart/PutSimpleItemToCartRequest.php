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

use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class PutSimpleItemToCartRequest implements RequestInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $productCode;

    /** @var int */
    protected $quantity;

    protected function __construct(?string $token, ?string $productCode, ?int $quantity)
    {
        $this->token = $token;
        $this->productCode = $productCode;
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

    public static function fromArray(array $item): self
    {
        return new self(
            $item['token'] ?? null,
            $item['productCode'] ?? null,
            $item['quantity'] ?? null
        );
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self(
            $request->attributes->get('token'),
            $request->request->get('productCode'),
            $request->request->getInt('quantity', 1)
        );
    }

    public function getCommand(): CommandInterface
    {
        return new PutSimpleItemToCart($this->token, $this->productCode, $this->quantity);
    }
}
