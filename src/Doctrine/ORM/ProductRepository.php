<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Doctrine\ORM;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Doctrine\ORM\QueryBuilder;

final class ProductRepository extends BaseProductRepository
{
    public function createShopListQueryBuilder(
        ChannelInterface $channel,
        TaxonInterface $taxon,
        string $locale,
        array $sorting = [],
        bool $includeAllDescendants = false
    ): QueryBuilder {
        $queryBuilder = parent::createShopListQueryBuilder($channel, $taxon, $locale, $sorting, $includeAllDescendants);

        $queryBuilder->addSelect('productTaxon');

        return $queryBuilder;
    }
}
