<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\AddressBook;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\ShopApiPlugin\Command\UpdateAddress;
use Sylius\ShopApiPlugin\Factory\AddressBookViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Model\Address;
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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @var ValidationErrorViewFactoryInterface
     */
    private $validationErrorViewFactory;

    /**
     * @var AddressBookViewFactoryInterface
     */
    private $addressBookViewFactory;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param ViewHandlerInterface $viewHandler
     * @param ValidatorInterface $validator
     * @param CommandBus $bus
     * @param ValidationErrorViewFactoryInterface $validationErrorViewFactory
     * @param AddressBookViewFactoryInterface $addressViewFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        ValidatorInterface $validator,
        CommandBus $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        AddressBookViewFactoryInterface $addressViewFactory,
        AddressRepositoryInterface $addressRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->viewHandler = $viewHandler;
        $this->validator = $validator;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->addressBookViewFactory = $addressViewFactory;
        $this->addressRepository = $addressRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request, $id): Response
    {
        $addressModel = Address::createFromRequest($request);

        $validationResults = $this->validator->validate($addressModel);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        /** @var ShopUserInterface $customer */
        $shopUser = $this->tokenStorage->getToken()->getUser();

        $this->bus->handle(new UpdateAddress($addressModel, $shopUser->getEmail(), $id));

        /** @var AddressInterface $updatedAddress */
        $updatedAddress = $this->addressRepository->findOneBy(['id' => $id]);

        return $this->viewHandler->handle(View::create(
            $this->addressBookViewFactory->create($updatedAddress, $shopUser->getCustomer()),
            Response::HTTP_OK)
        );
    }
}
