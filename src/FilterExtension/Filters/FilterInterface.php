<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\FilterExtension\Filters;

use Doctrine\ORM\QueryBuilder;
use Sylius\ShopApiPlugin\FilterExtension\Util\QueryNameGeneratorInterface;

/**
 * Filter applied to the query.
 *
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
interface FilterInterface
{
    /**
     * Sets the conditions to the filter.
     *
     * @param mixed $conditions Tells the Filter what condition to apply.
     */
    public function applyFilter(array $conditions, string $resourceClass, QueryBuilder $queryBuilder): void;
}
