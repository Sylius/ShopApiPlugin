<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Checkout;

use Sylius\ShopApiPlugin\Command\Cart\CompleteOrder;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class CompleteOrderRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $notes;
    protected $points;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->notes = $request->request->get('notes');
        $this->points = $request->request->get('points');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new CompleteOrder($this->token, $this->notes, $this->points);
    }

    public function getToken(): string
    {
        return $this->token;
    }
    public function getNotes(): string
    {
        return $this->notes;
    }
    public function getPoints()
    {
        return $this->points;
    }
}
