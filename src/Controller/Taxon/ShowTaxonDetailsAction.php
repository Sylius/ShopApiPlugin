<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Taxon;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\TaxonDetailsViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowTaxonDetailsAction
{
    /** @var TaxonRepositoryInterface */
    private $taxonRepository;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var TaxonDetailsViewFactoryInterface */
    private $taxonViewFactory;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ViewHandlerInterface $viewHandler,
        TaxonDetailsViewFactoryInterface $taxonViewFactory,
        ChannelRepositoryInterface $channelRepository,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->viewHandler = $viewHandler;
        $this->taxonViewFactory = $taxonViewFactory;
        $this->channelRepository = $channelRepository;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    public function __invoke(Request $request): Response
    {
        $code = $request->attributes->get('code');

        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($request->attributes->get('channelCode'));
        $locale = $this->supportedLocaleProvider->provide($request->query->get('locale'), $channel);

        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);

        if (null === $taxon) {
            throw new NotFoundHttpException(sprintf('Taxon with code %s has not been found.', $code));
        }

        return $this->viewHandler->handle(View::create($this->taxonViewFactory->create($taxon, $locale), Response::HTTP_OK));
    }
}
