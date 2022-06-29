<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class LoggedInCustomerDetailsActionTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_shows_currently_logged_in_customer_details(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $this->logInUser('oliver@queen.com', '123password');

        $response = $this->getCustomerDetails();
        $this->assertResponse($response, 'customer/logged_in_customer_details_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_customer_details_without_being_logged_in(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $response = $this->getCustomerDetails();
        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    private function getCustomerDetails(): Response
    {
        $this->client->request('GET', '/shop-api/me', [], [], self::CONTENT_TYPE_HEADER);

        return $this->client->getResponse();
    }
}
