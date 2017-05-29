<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\TaxonViewFactoryInterface;
use Sylius\ShopApiPlugin\View\TaxonView;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TaxonController extends Controller
{
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var TaxonViewFactoryInterface
     */
    private $taxonViewFactory;

    /**
     * @param TaxonRepositoryInterface $taxonRepository
     * @param ViewHandlerInterface $viewHandler
     * @param TaxonViewFactoryInterface $taxonViewFactory
     */
    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ViewHandlerInterface $viewHandler,
        TaxonViewFactoryInterface $taxonViewFactory
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->viewHandler = $viewHandler;
        $this->taxonViewFactory = $taxonViewFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showDetailsAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $taxonSlug = $request->attributes->get('slug');
        $locale = $request->query->get('locale');

        $taxon = $this->taxonRepository->findOneBySlug($taxonSlug, $locale);

        if (null === $taxon) {
            throw new NotFoundHttpException(sprintf('Taxon with slug %s has not been found in %s locale.', $taxonSlug, $locale));
        }

        return $this->viewHandler->handle(View::create($this->buildTaxonView($taxon, $locale), Response::HTTP_OK));
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showTreeAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $locale = $request->query->get('locale');

        $taxons = $this->taxonRepository->findRootNodes();
        $taxonViews = [];

        /** @var TaxonInterface $taxon */
        foreach ($taxons as $taxon) {
            $taxonViews[] = $this->buildTaxonView($taxon, $locale);
        }

        return $this->viewHandler->handle(View::create($taxonViews, Response::HTTP_OK));
    }

    /**
     * @param TaxonInterface $taxon
     * @param string $locale
     *
     * @return TaxonView
     */
    private function buildTaxonView(TaxonInterface $taxon, string $locale): \Sylius\ShopApiPlugin\View\TaxonView
    {
        $taxonView = $this->taxonViewFactory->create($taxon, $locale);

        foreach ($taxon->getChildren() as $childTaxon) {
            $taxonView->children[] = $this->buildTaxonView($childTaxon, $locale);
        }

        return $taxonView;
    }
}
