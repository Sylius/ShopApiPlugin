<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Product;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ShowLatestApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_latest_products_with_default_count(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/product-latest/', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_latest_4_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_latest_2_products(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/product-latest/', ['limit' => 2], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_latest_2_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_latest_products_without_disabled_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->get('sylius.repository.product');

        /** @var ObjectManager $productManager */
        $productManager = $this->get('sylius.manager.product');

        /** @var ProductInterface $simpleProduct */
        $simpleProduct = $productRepository->findOneBy(['code' => 'LOGAN_SHOES_CODE']);

        $simpleProduct->disable();
        $productManager->persist($simpleProduct);
        $productManager->flush();

        $this->client->request('GET', '/shop-api/product-latest/', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_latest_without_disabled_simple_product_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_latest_products_without_disabled_variant(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        /** @var ProductVariantRepositoryInterface $productVariantRepository */
        $productVariantRepository = $this->get('sylius.repository.product_variant');

        /** @var ObjectManager $productVariantManager */
        $productVariantManager = $this->get('sylius.manager.product_variant');

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $productVariantRepository->findOneBy(['code' => 'SMALL_RED_LOGAN_HAT_CODE']);

        $productVariant->disable();

        $productVariantManager->persist($productVariant);
        $productVariantManager->flush();

        $this->client->request('GET', '/shop-api/product-latest/', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product/product_list_latest_without_disabled_variant_response', Response::HTTP_OK);
    }
}
