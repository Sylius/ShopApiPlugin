<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\AddressBook;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\SetDefaultAddress;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInUserProviderInterface;
use Sylius\ShopApiPlugin\Request\SetDefaultAddressRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SetDefaultAddressAction
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ValidationErrorViewFactoryInterface
     */
    private $validationErrorViewFactory;

    /**
     * @var LoggedInUserProviderInterface
     */
    private $loggedInUserProvider;

    /**
     * @param ViewHandlerInterface                $viewHandler
     * @param CommandBus                          $bus
     * @param ValidatorInterface                  $validator
     * @param ValidationErrorViewFactoryInterface $validationErrorViewFactory
     * @param TokenStorageInterface               $tokenStorage
     * @param LoggedInUserProviderInterface       $loggedInUserProvider
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        LoggedInUserProviderInterface $loggedInUserProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->loggedInUserProvider = $loggedInUserProvider;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $setDefaultAddressRequest = new SetDefaultAddressRequest($request);

        $validationResults = $this->validator->validate($setDefaultAddressRequest);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(
                View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST)
            );
        }

        try {
            /** @var ShopUserInterface $user */
            $user = $this->loggedInUserProvider->provide();
        } catch (TokenNotFoundException $exception) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        if ($user->getCustomer() !== null) {
            $this->bus->handle(new SetDefaultAddress($request->attributes->get('id'), $user->getEmail()));

            $view = View::create(null, Response::HTTP_NO_CONTENT);
        } else {
            $view = View::create(['message' => 'The user is not a customer'], Response::HTTP_BAD_REQUEST);
        }

        return $this->viewHandler->handle($view);
    }
}
