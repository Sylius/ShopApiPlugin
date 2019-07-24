<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Utils;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

trait ShopUserLoginTrait
{
    /** @var Client */
    protected $client;

    protected function logInUser(string $username, string $password): void
    {
        $data =
<<<JSON
        {
            "_username": "$username",
            "_password": "$password"
        }
JSON;

        $this->client->request('POST', '/shop-api/login_check', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);
        Assert::assertSame($this->client->getResponse()->getStatusCode(), Response::HTTP_OK);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $response['token']));
    }
}
