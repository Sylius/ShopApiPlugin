<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\View\TaxonDetailsView;

interface TaxonDetailsViewFactoryInterface
{
    public function create(TaxonInterface $taxon, string $localeCode): TaxonDetailsView;
}
