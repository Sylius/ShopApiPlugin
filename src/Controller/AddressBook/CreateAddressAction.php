<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\AddressBook;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Command\CreateAddress;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Model\Address;
use Sylius\ShopApiPlugin\Provider\LoggedInUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateAddressAction
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
    private $currentUserProvider;

    /**
     * @param ViewHandlerInterface                $viewHandler
     * @param CommandBus                          $bus
     * @param ValidatorInterface                  $validator
     * @param ValidationErrorViewFactoryInterface $validationErrorViewFactory
     * @param LoggedInUserProviderInterface       $currentUserProvider
     */
    public function __construct(
        ViewHandlerInterface $viewHandler,
        CommandBus $bus,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        LoggedInUserProviderInterface $currentUserProvider
    ) {
        $this->viewHandler                = $viewHandler;
        $this->bus                        = $bus;
        $this->validator                  = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->currentUserProvider        = $currentUserProvider;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $addressModel = Address::createFromRequest($request);

        $validationResults = $this->validator->validate($addressModel);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(
                View::create($this->validationErrorViewFactory->create($validationResults), Response::HTTP_BAD_REQUEST)
            );
        }

        try {
            /** @var ShopUserInterface $user */
            $user = $this->currentUserProvider->provide();
        } catch (TokenNotFoundException $exception) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }


        if ($user->getCustomer() !== null) {
            $this->bus->handle(new CreateAddress($addressModel, $user->getEmail()));
            $view = View::create(null, Response::HTTP_NO_CONTENT);
        } else {
            $view = View::create(['message' => 'The user is not a customer'], Response::HTTP_BAD_REQUEST);
        }

        return $this->viewHandler->handle($view);
    }
}
