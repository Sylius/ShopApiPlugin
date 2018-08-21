<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;

final class TaxonShowTreeApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_tree_of_all_taxons()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxons/', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/all_taxons_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_tree_of_all_taxons_in_different_language()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxons/?locale=de_DE', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/german_all_taxons_response', Response::HTTP_OK);
    }
}
