<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\FilterExtension;

use Doctrine\ORM\QueryBuilder;

/**
 * Applies filters to a query.
 *
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
interface FilterExtensionInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     * @param array|null $filterConditions
     */
    public function applyFilters(QueryBuilder $queryBuilder, string $resourceClass, ?array $filterConditions): void;
}
