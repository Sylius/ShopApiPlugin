<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\SyliusShopApiPlugin\Factory\CustomerViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Request\UpdateCustomerRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

final class UpdateCustomerAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ValidatorInterface */
    private $validator;

    /** @var CommandBus */
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
        CommandBus $bus,
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

    /**
     * @param Request $request
     *
     * @return Response
     */
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
        $this->bus->handle($updateCustomerCommand);

        return $this->viewHandler->handle(View::create(
            $this->customerViewFactory->create($customer),
            Response::HTTP_OK)
        );
    }
}
