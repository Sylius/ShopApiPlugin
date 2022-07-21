<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Taxon;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\View\Taxon\TaxonDetailsView;

interface TaxonDetailsViewFactoryInterface
{
    public function create(TaxonInterface $taxon, string $localeCode): TaxonDetailsView;
}
