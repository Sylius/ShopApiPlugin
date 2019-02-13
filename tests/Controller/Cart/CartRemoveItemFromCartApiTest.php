<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class CartRemoveItemFromCartApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_deletes_item()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 1));

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');

        $order = $orderRepository->findOneBy(['tokenValue' => $token]);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems()->first();

        $this->client->request('DELETE', '/shop-api/WEB_GB/carts/SDAOSLEFNWU35H3QLI5325/items/' . $orderItem->getId(), [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_after_deleting_an_item', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_exception_if_cart_item_has_not_been_found()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $this->client->request('DELETE', '/shop-api/WEB_GB/carts/SDAOSLEFNWU35H3QLI5325/items/420', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_item_has_not_been_found_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_remove_item_from_cart_in_non_existent_channel()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $this->client->request('DELETE', '/shop-api/SPACE_KLINGON/carts/SDAOSLEFNWU35H3QLI5325/items/420', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_reprocesses_the_order_after_deleting_an_item()
    {
        $this->loadFixturesFromFiles(['shop.yml', 'promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 1));
        $bus->handle(new PutVariantBasedConfigurableItemToCart($token, 'LOGAN_HAT_CODE', 'SMALL_RED_LOGAN_HAT_CODE', 10));

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');

        $order = $orderRepository->findOneBy(['tokenValue' => $token]);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems()->last();

        $this->client->request('DELETE', '/shop-api/WEB_GB/carts/SDAOSLEFNWU35H3QLI5325/items/' . $orderItem->getId(), [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/reprocessed_cart_after_deleting_an_item', Response::HTTP_OK);
    }
}
