<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Product;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ShowDetailsBySlugApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_simple_product_details_page(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-slug/logan-mug', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/simple_product_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_without_taxon_details_page(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-slug/logan-shoes', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_without_taxons_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_throws_a_not_found_exception_if_channel_has_not_been_found(): void
    {
        $this->client->request('GET', '/shop-api/products/by-slug/logan-mug', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_throws_a_not_found_exception_if_product_has_not_been_found(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-slug/some-weird-stuff', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_has_not_been_found_for_given_slug_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_shows_simple_product_details_page_in_different_locale(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-slug/logan-becher?locale=de_DE', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_simple_product_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_variant_details_page(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-slug/logan-t-shirt', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_with_variant_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_variant_details_without_disabled_product_variante_page(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        /** @var ProductVariantRepositoryInterface $productVariantRepository */
        $productVariantRepository = $this->get('sylius.repository.product_variant');

        /** @var \Doctrine\Persistence\ObjectManager $productVariantManager */
        $productVariantManager = $this->get('sylius.manager.product_variant');

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $productVariantRepository->findOneBy(['code' => 'SMALL_LOGAN_T_SHIRT_CODE']);

        if (method_exists($productVariant, 'disable')) {
            $productVariant->disable();

            $productVariantManager->persist($productVariant);
            $productVariantManager->flush();
        }

        $this->client->request('GET', '/shop-api/products/by-slug/logan-t-shirt', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        if (method_exists($productVariant, 'disable')) {
            $this->assertResponse($response, 'product/product_with_variant_details_without_disabled_product_variant_page', Response::HTTP_OK);
        } else {
            $this->assertResponse($response, 'product/product_with_variant_details_page', Response::HTTP_OK);
        }
    }

    /**
     * @test
     */
    public function it_shows_product_with_options_details_page(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-slug/logan-hat', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_with_options_details_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_with_options_details_page_in_different_locale(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/products/by-slug/logan-hut?locale=de_DE', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/german_product_with_options_details_page', Response::HTTP_OK);
    }
}
