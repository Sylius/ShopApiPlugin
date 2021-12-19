<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Country;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Country\CountryViewFactoryInterface;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShowCountriesAction
{
    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CountryViewFactoryInterface */
    private $countryViewFactory;

    /** @var RequestBasedLocaleProviderInterface */
    private $requestBasedLocaleProvider;

    public function __construct(
        RepositoryInterface $countryRepository,
        ViewHandlerInterface $viewHandler,
        CountryViewFactoryInterface $countryViewFactory,
        RequestBasedLocaleProviderInterface $requestBasedLocaleProvider
    ) {
        $this->countryRepository = $countryRepository;
        $this->viewHandler = $viewHandler;
        $this->countryViewFactory = $countryViewFactory;
        $this->requestBasedLocaleProvider = $requestBasedLocaleProvider;
    }

    public function __invoke(Request $request): Response
    {
        $localeCode = $this->requestBasedLocaleProvider->getLocaleCode($request);

        $countries = $this->countryRepository->findAll();
        $countryViews = [];

        /** @var CountryInterface $country */
        foreach ($countries as $country) {
            $countryViews[] = $this->countryViewFactory->create($country, [], $localeCode);
        }

        return $this->viewHandler->handle(View::create($countryViews, Response::HTTP_OK));
    }
}
