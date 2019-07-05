<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Encoder;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

class GuestOrderJWTEncoder implements GuestOrderJWTEncoderInterface
{
    /** @var JWTEncoderInterface */
    protected $JWTEncoder;

    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    public function __construct(JWTEncoderInterface $JWTEncoder, OrderRepositoryInterface $orderRepository)
    {
        $this->JWTEncoder = $JWTEncoder;
        $this->orderRepository = $orderRepository;
    }

    public function encode(OrderInterface $order): string
    {
        $data = ['orderToken' => $order->getTokenValue()];

        return $this->JWTEncoder->encode($data);
    }

    public function decode(string $jwt): OrderInterface
    {
        $data = $this->JWTEncoder->decode($jwt);

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByTokenValue($data['orderToken']);

        return $order;
    }
}
