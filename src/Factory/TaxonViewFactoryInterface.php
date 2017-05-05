<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\View\TaxonView;

interface TaxonViewFactoryInterface
{
    /**
     * @param TaxonInterface $taxon
     * @param string $locale
     *
     * @return TaxonView
     */
    public function create(TaxonInterface $taxon, $locale);
}
