<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\FilterExtension;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Sylius\ShopApiPlugin\FilterExtension\Filters\FilterInterface;

/**
 * Applies filters to a query.
 *
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 */
class FilterExtension implements FilterExtensionInterface
{

    /**
     * @var ArrayCollection
     */
    private $filters;

    public function __construct()
    {
        $this->filters = new ArrayCollection();
    }

    /**
     * Add a filter
     *
     * @param FilterInterface $filter
     *
     * @internal
     */
    public function addFilter(FilterInterface $filter)
    {
        if ($this->filters->contains($filter)) {
            return;
        }

        $this->filters->add($filter);
    }

    /**
     * Applies the filters.
     *
     * {@inheritdoc}
     */
    public function applyFilters(QueryBuilder $queryBuilder, string $resourceClass, ?array $filterConditions): void
    {
        if (empty($filterConditions)) {
            return;
        }

        foreach ($this->filters as $filter) {
            /** @var FilterInterface $filter */
            $filter->applyFilter($filterConditions, $resourceClass, $queryBuilder);
        }
    }
}
