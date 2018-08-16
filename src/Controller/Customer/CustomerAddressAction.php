<?php
declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Factory\AddressBookViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class CustomerAddressAction
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
     * @var AddressBookViewFactoryInterface
     */
    private $addressBookViewFactory;

    /**
     * @param ViewHandlerInterface            $viewHandler
     * @param TokenStorageInterface           $tokenStorage
     * @param AddressBookViewFactoryInterface $addressBookViewFactory
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        TokenStorageInterface $tokenStorage,
        AddressBookViewFactoryInterface $addressBookViewFactory
    ) {
        $this->viewHandler            = $viewHandler;
        $this->tokenStorage           = $tokenStorage;
        $this->addressBookViewFactory = $addressBookViewFactory;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ShopUserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();

        Assert::isInstanceOf($user, ShopUserInterface::class);
        $customer = $user->getCustomer();

        /** @var Customer $customer */
        Assert::isInstanceOf($customer, Customer::class);

        return $this->viewHandler->handle(
            View::create($this->addressBookViewFactory->create(
                $customer->getDefaultAddress(),
                $customer->getAddresses()
            ),Response::HTTP_OK)
        );
    }

}
