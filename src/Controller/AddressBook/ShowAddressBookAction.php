<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\AddressBook;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Factory\AddressBook\AddressBookViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class ShowAddressBookAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInUserProvider;

    /** @var AddressBookViewFactoryInterface */
    private $addressBookViewFactory;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        LoggedInShopUserProviderInterface $loggedInUserProvider,
        AddressBookViewFactoryInterface $addressBookViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->addressBookViewFactory = $addressBookViewFactory;
    }

    /** Returns the list of addresses that is stored in the user's address book */
    public function __invoke(): Response
    {
        try {
            /** @var ShopUserInterface $user */
            $user = $this->loggedInUserProvider->provide();
        } catch (TokenNotFoundException $exception) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        $customer = $user->getCustomer();

        if ($customer instanceof Customer) {
            $addressViews = $customer->getAddresses()->map(
                function (AddressInterface $address) use ($customer) {
                    return $this->addressBookViewFactory->create($address, $customer);
                }
            );

            return $this->viewHandler->handle(View::create($addressViews, Response::HTTP_OK));
        }

        return $this->viewHandler->handle(View::create(['message' => 'The user is not a customer'], Response::HTTP_BAD_REQUEST));
    }
}
