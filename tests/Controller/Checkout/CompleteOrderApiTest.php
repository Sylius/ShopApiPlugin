<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Checkout;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AddressOrder;
use Sylius\ShopApiPlugin\Command\Cart\ChoosePaymentMethod;
use Sylius\ShopApiPlugin\Command\Cart\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Command\Cart\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Command\Cart\RemoveItemFromCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class CompleteOrderApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_allows_to_complete_checkout(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

        $data =
<<<JSON
        {
            "email": "example@cusomer.com"
        }
JSON;

        $this->client->enableProfiler();

        $response = $this->complete($token, $data);
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());
    }

    /**
     * @test
     */
    public function it_allows_to_complete_checkout_with_notes(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

        $data =
<<<JSON
        {
            "email": "example@cusomer.com",
            "notes": "BRING IT AS FAST AS YOU CAN, PLEASE!"
        }
JSON;

        $response = $this->complete($token, $data);
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_complete_checkout_with_shipping_skipped(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        /** @var ChannelInterface|null $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByCode('WEB_GB');
        $channel->setSkippingShippingStepAllowed(true);

        /** @var ProductInterface|null $product */
        $product = $this->get('sylius.repository.product')->findOneByCode('LOGAN_MUG_CODE');
        $product->getVariants()->first()->setShippingRequired(false);

        $this->get('doctrine.orm.entity_manager')->flush();

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

        $data =
<<<JSON
        {
            "email": "example@cusomer.com",
            "notes": "BRING IT AS FAST AS YOU CAN, PLEASE!"
        }
JSON;

        $response = $this->complete($token, $data);
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_complete_checkout_with_payment_skipped(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'FREE_DOWNLOAD', 5));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'FREE'));

        $data =
<<<JSON
        {
            "email": "example@cusomer.com",
            "notes": "BRING IT AS FAST AS YOU CAN, PLEASE!"
        }
JSON;

        $response = $this->complete($token, $data);
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_complete_checkout_without_email_for_logged_in_customer(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml', 'customer.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

        $this->logInUserWithCart('oliver@queen.com', '123password', $token);

        $response = $this->complete($token);
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_disallows_users_to_complete_checkout_for_user_with_account_without_loggin_in(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml', 'customer.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

        $data =
<<<JSON
        {
            "email": "oliver@queen.com",
            "notes": "BRING IT AS FAST AS YOU CAN, PLEASE!"
        }
JSON;

        $response = $this->complete($token, $data);
        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_disallows_users_to_complete_checkout_for_someone_else(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml', 'customer.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

        $this->logInUser('oliver@queen.com', '123password');

        $data =
<<<JSON
        {
            "email": "example@cusomer.com"
        }
JSON;

        $response = $this->complete($token, $data);
        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_disallows_to_complete_checkout_with_invalid_cart_token(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $data =
<<<JSON
        {
            "email": "example@cusomer.com",
            "notes": "BRING IT AS FAST AS YOU CAN, PLEASE!"
        }
JSON;

        $response = $this->complete($token, $data);
        $this->assertResponse($response, 'checkout/cart_does_not_exist', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_disallows_to_complete_checkout_with_invalid_state(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml', 'customer.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<JSON
        {
            "email": "example@cusomer.com",
            "notes": "BRING IT AS FAST AS YOU CAN, PLEASE!"
        }
JSON;

        $response = $this->complete($token, $data);
        $this->assertResponse($response, 'checkout/cart_not_ready_for_checkout', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_disallows_to_complete_checkout_if_product_is_disabled(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

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
            "email": "example@cusomer.com"
        }
JSON;

        $response = $this->complete($token, $data);
        $this->assertResponse($response, 'checkout/cart_failed_checkout_product_not_eligible_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_disallows_to_complete_checkout_if_product_variant_is_disabled(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutVariantBasedConfigurableItemToCart($token, 'LOGAN_T_SHIRT_CODE', 'LARGE_LOGAN_T_SHIRT_CODE', 1));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

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
            "email": "example@cusomer.com"
        }
JSON;

        $response = $this->complete($token, $data);

        $this->assertResponse($response, 'checkout/cart_failed_checkout_product_variant_not_eligible_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_disallows_to_complete_checkout_if_chart_is_empty(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 1));
        $bus->dispatch(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ])
        ));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');

        $order = $orderRepository->findOneBy(['tokenValue' => $token]);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems()->first();

        $bus->dispatch(new RemoveItemFromCart($token, $orderItem->getId()));

        $data =
<<<JSON
        {
            "email": "example@cusomer.com"
        }
JSON;

        $response = $this->complete($token, $data);
        $this->assertResponse($response, 'checkout/cart_empty_response', Response::HTTP_BAD_REQUEST);
    }

    private function complete(string $token, ?string $data = null): Response
    {
        $this->client->request(
            'PUT',
            sprintf('/shop-api/checkout/%s/complete', $token),
            [],
            [],
            self::CONTENT_TYPE_HEADER,
            $data
        );

        return $this->client->getResponse();
    }
}
