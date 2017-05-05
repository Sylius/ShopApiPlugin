<?php

namespace Sylius\ShopApiPlugin\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\TaxonView;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TaxonController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showDetailsAction(Request $request)
    {
        /** @var TaxonRepositoryInterface $taxonRepository */
        $taxonRepository = $this->get('sylius.repository.taxon');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');

        $taxonSlug = $request->attributes->get('slug');
        $locale = $request->query->get('locale');

        $taxon = $taxonRepository->findOneBySlug($taxonSlug, $locale);

        if (null === $taxon) {
            throw new NotFoundHttpException(sprintf('Taxon with slug %s has not been found in %s locale.', $taxonSlug, $locale));
        }

        return $viewHandler->handle(View::create($this->buildTaxonView($taxon, $locale), Response::HTTP_OK));
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showTreeAction(Request $request)
    {
        /** @var TaxonRepositoryInterface $taxonRepository */
        $taxonRepository = $this->get('sylius.repository.taxon');
        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');

        $locale = $request->query->get('locale');

        $taxons = $taxonRepository->findRootNodes();
        $taxonViews = [];

        /** @var TaxonInterface $taxon */
        foreach ($taxons as $taxon) {
            $taxonViews[] = $this->buildTaxonView($taxon, $locale);
        }

        return $viewHandler->handle(View::create($taxonViews, Response::HTTP_OK));
    }

    /**
     * @param TaxonInterface $taxon
     * @param string $locale
     *
     * @return TaxonView
     */
    private function buildTaxonView(TaxonInterface $taxon, $locale)
    {
        /** @var ImageViewFactoryInterface $imageViewFactory */
        $imageViewFactory = $this->get('sylius.shop_api_plugin.factory.image_view_factory');

        $taxonView = new TaxonView();
        $taxonView->name = $taxon->getTranslation($locale)->getName();
        $taxonView->code = $taxon->getCode();
        $taxonView->slug = $taxon->getTranslation($locale)->getSlug();
        $taxonView->description = $taxon->getTranslation($locale)->getDescription();
        $taxonView->position = $taxon->getPosition();

        /** @var ImageInterface $image */
        foreach ($taxon->getImages() as $image) {
            $taxonView->images[] = $imageViewFactory->create($image);
        }

        foreach ($taxon->getChildren() as $childTaxon) {
            $taxonView->children[] = $this->buildTaxonView($childTaxon, $locale);
        }

        return $taxonView;
    }
}
