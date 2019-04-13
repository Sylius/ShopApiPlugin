<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Factory\Customer\CustomerViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\Customer\UpdateCustomerRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ValidatorInterface $validator,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CustomerViewFactoryInterface $customerViewFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->viewHandler = $viewHandler;
        $this->validator = $validator;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->customerViewFactory = $customerViewFactory;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ShopUserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();

        Assert::isInstanceOf($user, ShopUserInterface::class);

        $customer = $user->getCustomer();
        $updateCustomerRequest = new UpdateCustomerRequest($request);

        $validationResults = $this->validator->validate($updateCustomerRequest, null, 'sylius_customer_profile_update');

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST));
        }

        $updateCustomerCommand = $updateCustomerRequest->getCommand();
        $this->bus->dispatch($updateCustomerCommand);

        return $this->viewHandler->handle(View::create(
            $this->customerViewFactory->create($customer),
            Response::HTTP_OK
        ));
    }
}
