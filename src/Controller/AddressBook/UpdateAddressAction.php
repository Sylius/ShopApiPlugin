<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\AddressBook;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UpdateAddressAction
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ViewHandlerInterface $viewHandler
     * @param TokenStorageInterface $tokenStorage
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        TokenStorageInterface $tokenStorage,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->viewHandler = $viewHandler;
        $this->tokenStorage = $tokenStorage;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    public function __invoke(Request $request): Response
    {
        return $this->viewHandler->handle(View::create([], Response::HTTP_OK));
    }
}
