<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Taxon;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\TaxonDetailsViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

final class ShowTaxonDetailsAction
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
     * @var TaxonDetailsViewFactoryInterface
     */
    private $taxonViewFactory;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ViewHandlerInterface $viewHandler,
        TaxonDetailsViewFactoryInterface $taxonViewFactory
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->viewHandler = $viewHandler;
        $this->taxonViewFactory = $taxonViewFactory;
    }

    public function __invoke(Request $request): Response
    {
        $code = $request->attributes->get('code');
        $locale = $request->query->get('locale');

        if (null === $locale) {
            throw new BadRequestHttpException('You didn\'t provide a locale!');
        }

        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);

        if (null === $taxon) {
            throw new NotFoundHttpException(sprintf('Taxon with code %s has not been found.', $code));
        }

        return $this->viewHandler->handle(View::create($this->taxonViewFactory->create($taxon, $locale), Response::HTTP_OK));
    }
}
