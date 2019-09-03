<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Taxon;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ShowTreeApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_tree_of_all_taxons_with_fallback_locale_from_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxons', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/all_taxons_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_tree_of_all_taxons_in_different_language(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/taxons?locale=de_DE', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'taxon/german_all_taxons_response', Response::HTTP_OK);
    }
}
