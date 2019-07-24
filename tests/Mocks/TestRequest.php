<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Mocks;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

final class TestRequest implements RequestInterface
{
    /** @var string */
    private $token;

    private function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new TestCommand($this->token);
    }
}
