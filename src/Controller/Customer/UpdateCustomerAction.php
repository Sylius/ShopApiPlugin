<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Factory\Customer\CustomerViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProvider;
use Sylius\ShopApiPlugin\Request\Customer\UpdateCustomerRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

final class UpdateCustomerAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ValidatorInterface */
    private $validator;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CustomerViewFactoryInterface */
    private $customerViewFactory;

    /** @var LoggedInShopUserProvider */
    private $loggedInUserProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ValidatorInterface $validator,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CustomerViewFactoryInterface $customerViewFactory,
        LoggedInShopUserProvider $loggedInUserProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->validator = $validator;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->customerViewFactory = $customerViewFactory;
        $this->loggedInUserProvider = $loggedInUserProvider;
    }

    public function __invoke(Request $request): Response
    {
        $updateCustomerRequest = new UpdateCustomerRequest($request);

        $validationResults = $this->validator->validate($updateCustomerRequest, null, 'sylius_customer_profile_update');

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        $this->bus->dispatch($updateCustomerRequest->getCommand());

        $customer = $this->loggedInUserProvider->provide()->getCustomer();
        Assert::notNull($customer);

        return $this->viewHandler->handle(View::create(
            $this->customerViewFactory->create($customer),
            Response::HTTP_OK
        ));
    }
}
