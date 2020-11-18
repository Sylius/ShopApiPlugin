<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Command\Cart\PutVariantBasedConfigurableItemToCart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ChangeItemQuantityApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_does_not_allow_to_change_quantity_if_cart_does_not_exists(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $data =
<<<JSON
        {
            "quantity": 5
        }
JSON;
        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_and_cart_item_not_exist_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_changes_item_quantity(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 3));

        $data =
<<<JSON
        {
            "quantity": 5
        }
JSON;
        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/' . $this->getFirstOrderItemId($token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_set_quantity_lower_than_one(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 3));

        $data =
<<<JSON
        {
            "quantity": 0
        }
JSON;
        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/' . $this->getFirstOrderItemId($token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_quantity_lower_than_one_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_change_quantity_without_quantity_defined(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 3));

        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/' . $this->getFirstOrderItemId($token), [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_quantity_lower_than_one_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_change_quantity_if_cart_item_does_not_exists(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<JSON
        {
            "quantity": 5
        }
JSON;
        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/420', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_item_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_change_quantity_if_cart_item_is_disabled(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 1));

        /** @var ProductRepository $productRepository */
        $productRepository = $this->get('sylius.repository.product');

        /** @var ObjectManager $productManager */
        $productManager = $this->get('sylius.manager.product');

        /** @var Product $product */
        $product = $productRepository->findOneBy(['code' => 'LOGAN_MUG_CODE']);
        $product->setEnabled(false);

        $productManager->persist($product);
        $productManager->flush();

        $data =
<<<JSON
        {
            "quantity": 5
        }
JSON;
        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/' . $this->getFirstOrderItemId($token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_item_not_eligible_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_change_quantity_if_cart_item_variant_is_disabled(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutVariantBasedConfigurableItemToCart($token, 'LOGAN_T_SHIRT_CODE', 'LARGE_LOGAN_T_SHIRT_CODE', 1));

        /** @var ProductVariantRepository $productVariantRepository */
        $productVariantRepository = $this->get('sylius.repository.product_variant');

        /** @var ObjectManager $productVariantManager */
        $productVariantManager = $this->get('sylius.manager.product_variant');

        /** @var ProductVariant $productVariant */
        $productVariant = $productVariantRepository->findOneBy(['code' => 'LARGE_LOGAN_T_SHIRT_CODE']);
        $productVariant->setEnabled(false);

        $productVariantManager->persist($productVariant);
        $productVariantManager->flush();

        $data =
<<<JSON
        {
            "quantity": 5
        }
JSON;
        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/' . $this->getFirstOrderItemId($token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_item_variant_not_eligible_response', Response::HTTP_BAD_REQUEST);
    }

    private function getFirstOrderItemId(string $orderToken): string
    {
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');

        $order = $orderRepository->findOneBy(['tokenValue' => $orderToken]);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems()->first();

        return (string) $orderItem->getId();
    }
}
