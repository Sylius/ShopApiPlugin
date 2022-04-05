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

namespace Tests\Sylius\ShopApiPlugin\Controller\Taxon;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ShowDetailsApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_summary_of_a_chosen_taxon_with_fallback_locale_from_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxons/T_SHIRTS', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/one_of_taxons_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_chosen_taxon_with_strange_code(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxons/de%3Flol%3Dxd%23boom?locale=en_GB', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/strange_taxon_code_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_chosen_taxon_in_different_language(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxons/T_SHIRTS?locale=de_DE', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/german_one_of_taxons_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_chosen_taxon_in_different_language_based_on_header(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request(
            'GET',
            '/shop-api/taxons/T_SHIRTS',
            [],
            [],
            array_merge(
                self::CONTENT_TYPE_HEADER,
                ['HTTP_ACCEPT-LANGUAGE' => 'de_DE']
            )
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/german_one_of_taxons_response', Response::HTTP_OK);
    }
}
