<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaxonShowDetailsApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_summary_of_a_chosen_taxon()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxons/T_SHIRTS?locale=en_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/one_of_taxons_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_chosen_taxon_with_strange_code()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxons/de%3Flol%3Dxd%23boom?locale=en_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/strange_taxon_code_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_chosen_taxon_in_different_language()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxons/T_SHIRTS?locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/german_one_of_taxons_response', Response::HTTP_OK);
    }
}
