<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\AddressBook;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Factory\AddressBookViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class ShowAddressBookAction
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var LoggedInUserProviderInterface
     */
    private $loggedInUserProvider;

    /**
     * @var AddressBookViewFactoryInterface
     */
    private $addressBookViewFactory;

    /**
     * @param ViewHandlerInterface $viewHandler
     * @param LoggedInUserProviderInterface $loggedInUserProvider
     * @param AddressBookViewFactoryInterface $addressBookViewFactory
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        LoggedInUserProviderInterface $loggedInUserProvider,
        AddressBookViewFactoryInterface $addressBookViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->addressBookViewFactory = $addressBookViewFactory;
    }

    /**
     * Returns the list of addresses that is stored in the user's address book
     */
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

            $view = View::create($addressViews, Response::HTTP_OK);
        } else {
            $view = View::create(['message' => 'The user is not a customer'], Response::HTTP_BAD_REQUEST);
        }

        return $this->viewHandler->handle($view);
    }
}
