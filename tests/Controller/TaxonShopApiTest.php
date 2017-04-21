<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaxonShopApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_summary_of_a_chosen_taxon()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxons/t-shirts?locale=en_GB', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/one_of_taxons_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_chosen_taxon_in_different_language()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxons/t-shirts?locale=de_DE', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/german_one_of_taxons_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_tree_of_all_taxons()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxons/', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/all_taxons_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_tree_of_all_taxons_in_different_language()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxons/?locale=de_DE', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/german_all_taxons_response', Response::HTTP_OK);
    }
}
