<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\SyliusShopApiPlugin\View\TaxonView;

interface TaxonViewFactoryInterface
{
    public function create(TaxonInterface $taxon, string $locale): TaxonView;
}
