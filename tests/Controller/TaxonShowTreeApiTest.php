<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;

final class TaxonShowTreeApiTest extends JsonApiTestCase
{
//    /**
//     * @test
//     */
//    public function it_shows_tree_of_all_taxons_with_fallback_locale_from_channel()
//    {
//        $this->loadFixturesFromFiles(['shop.yml']);
//
//        $this->client->request('GET', '/shop-api/WEB_GB/taxons/', [], [], ['ACCEPT' => 'application/json']);
//
//        $response = $this->client->getResponse();
//
//        $this->assertResponse($response, 'taxon/all_taxons_response', Response::HTTP_OK);
//    }

    /**
     * @test
     */
    public function it_shows_tree_of_all_taxons_in_different_language()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/taxons/?locale=de_DE', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/german_all_taxons_response', Response::HTTP_OK);
    }
//
//    /**
//     * @test
//     */
//    public function it_does_not_show_taxons_tree_in_non_existent_channel()
//    {
//        $this->loadFixturesFromFiles(['shop.yml']);
//
//        $this->client->request('GET', '/shop-api/SPACE_KLINGON/taxons/', [], [], ['ACCEPT' => 'application/json']);
//
//        $response = $this->client->getResponse();
//
//        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
//    }
}
