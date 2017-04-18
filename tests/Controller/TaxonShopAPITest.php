<?php

namespace Tests\Acme\ExampleBundle\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaxonShopAPITest extends JsonApiTestCase
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

    public function it_shows_tree_of_all_taxons()
    {
        $this->client->request('GET', '/shop-api/taxons/', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/all_taxons_response', Response::HTTP_OK);
    }
}
