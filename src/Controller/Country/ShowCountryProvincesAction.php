<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Country;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Country\CountryViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Country\Province\ProvinceViewFactoryInterface;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use Sylius\ShopApiPlugin\View\Country\Province\ProvinceView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowCountryProvincesAction
{
    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CountryViewFactoryInterface */
    private $countryViewFactory;

    /** @var ProvinceViewFactoryInterface */
    private $provinceViewFactory;

    /** @var RequestBasedLocaleProviderInterface */
    private $requestBasedLocaleProvider;

    public function __construct(
        RepositoryInterface $countryRepository,
        ViewHandlerInterface $viewHandler,
        CountryViewFactoryInterface $countryViewFactory,
        ProvinceViewFactoryInterface $provinceViewFactory,
        RequestBasedLocaleProviderInterface $requestBasedLocaleProvider
    ) {
        $this->countryRepository = $countryRepository;
        $this->viewHandler = $viewHandler;
        $this->countryViewFactory = $countryViewFactory;
        $this->provinceViewFactory = $provinceViewFactory;
        $this->requestBasedLocaleProvider = $requestBasedLocaleProvider;
    }

    public function __invoke(Request $request): Response
    {
        $localeCode = $this->requestBasedLocaleProvider->getLocaleCode($request);

        $code = $request->attributes->get('code');
        /** @var CountryInterface|null $country */
        $country = $this->countryRepository->findOneBy(['code' => $code]);
        if (null === $country) {
            throw new NotFoundHttpException(sprintf('Country with code %s has not been found.', $code));
        }

        /** @var array $provinces */
        $provinces = $country->getProvinces();
        $provinceViews = [];

        /** @var ProvinceInterface $province */
        foreach ($provinces as $province) {
            $provinceViews[] = $this->buildProvinceView($province);
        }

        return $this->viewHandler->handle(View::create($this->countryViewFactory->create($country, $provinceViews, $localeCode), Response::HTTP_OK));
    }

    private function buildProvinceView(ProvinceInterface $province): ProvinceView
    {
        return $this->provinceViewFactory->create($province);
    }
}
