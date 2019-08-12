<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Mocks;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\ShopUserBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

final class TestShopUserBasedRequest implements ShopUserBasedRequestInterface
{
    /** @var string */
    private $token;

    /** @var string */
    private $email;

    public function __construct(Request $request, string $email)
    {
        $this->token = $request->attributes->get('token');
        $this->email = $email;
    }

    public static function fromHttpRequestAndShopUser(Request $request, ShopUserInterface $user): ShopUserBasedRequestInterface
    {
        return new self($request, $user->getEmail());
    }

    public function getCommand(): CommandInterface
    {
        return new TestShopUserBasedCommand($this->token, $this->email);
    }
}
