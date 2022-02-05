<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\Factory\Customer\CustomerViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class LoggedInCustomerDetailsAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInShopUserProvider;

    /** @var CustomerViewFactoryInterface */
    private $customerViewFactory;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        CustomerViewFactoryInterface $customerViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->loggedInShopUserProvider = $loggedInShopUserProvider;
        $this->customerViewFactory = $customerViewFactory;
    }

    public function __invoke(Request $request): Response
    {
        if (!$this->loggedInShopUserProvider->isUserLoggedIn()) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        $customer = $this->loggedInShopUserProvider->provide()->getCustomer();
        Assert::notNull($customer);

        return $this->viewHandler->handle(View::create($this->customerViewFactory->create($customer)));
    }
}
