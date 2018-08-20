<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\SyliusShopApiPlugin\View\TaxonDetailsView;

interface TaxonDetailsViewFactoryInterface
{
    public function create(TaxonInterface $taxon, string $localeCode): TaxonDetailsView;
}
