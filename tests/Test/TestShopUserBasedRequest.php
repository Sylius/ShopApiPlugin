<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Test;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\ShopUserBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class TestShopUserBasedRequest implements ShopUserBasedRequestInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $email;

    public function __construct(Request $request, string $email)
    {
        $this->token = $request->attributes->get('token');
        $this->email = $email;
    }

    public static function fromHttpRequestAndShopUser(Request $request, ?ShopUserInterface $user): ShopUserBasedRequestInterface
    {
        return new self($request, $user->getEmail());
    }

    public function getCommand(): CommandInterface
    {
        return new TestShopUserBasedCommand($this->token, $this->email);
    }
}
