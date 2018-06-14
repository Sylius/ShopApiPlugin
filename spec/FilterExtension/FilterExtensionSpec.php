<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\FilterExtension;

use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Product;
use Sylius\ShopApiPlugin\FilterExtension\FilterExtension;
use Sylius\ShopApiPlugin\FilterExtension\Filters\FilterInterface;

final class FilterExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FilterExtension::class);
    }

    function it_should_apply_filters(FilterInterface $filter, QueryBuilder $queryBuilder)
    {
        $ressourceClass = Product::class;
        $filterConditions = ['boolean'=>['attribute'=>true]];

        $filter->applyFilter($filterConditions, $ressourceClass, $queryBuilder)->shouldBeCalled();
        $this->addFilter($filter);
        $this->applyFilters($queryBuilder, $ressourceClass, $filterConditions);
    }

    function it_should_not_apply_anything(FilterInterface $filter, QueryBuilder $queryBuilder)
    {
        $ressourceClass = Product::class;
        $filterConditions = ['boolean'=>['attribute'=>true]];

        $filter->applyFilter($filterConditions, $ressourceClass, $queryBuilder)->shouldNotBeCalled();
        $this->applyFilters($queryBuilder, $ressourceClass, $filterConditions);
    }
}
