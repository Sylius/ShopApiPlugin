<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\FilterExtension\Filters;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\ShopApiPlugin\FilterExtension\Filters\BooleanFilter;

final class BooleanFilterSpec extends ObjectBehavior
{
    function let(ManagerRegistry $managerRegistry, LoggerInterface $logger)
    {
        $this->beConstructedWith($managerRegistry, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BooleanFilter::class);
    }

    function it_should_ignore_a_non_boolean_condition(QueryBuilder $queryBuilder)
    {
        $conditions = ['search'=>['enabled'=>true]];
        $resourceClass = Product::class;

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyFilter($conditions, $resourceClass, $queryBuilder);
    }

    function it_should_apply_a_boolean_condition(ManagerRegistry $managerRegistry, ObjectManager $om, ClassMetadata $classMetadata, LoggerInterface $logger, QueryBuilder $queryBuilder)
    {
        $conditions = ['boolean'=>['enabled'=>true]];
        $resourceClass = Product::class;


        // is property an association false
        $classMetadata->hasAssociation('enabled')->willReturn(false);
        // yes, this is a boolean
        $classMetadata->getTypeOfField('enabled')->willReturn('boolean');
        // is property mapped true
        $classMetadata->hasField('enabled')->willReturn(true);

        $om->getClassMetadata($resourceClass)->willReturn($classMetadata);
        $managerRegistry->getManagerForClass($resourceClass)->willReturn($om);

        $this->beConstructedWith($managerRegistry, $logger);
        $queryBuilder->getRootAliases()->shouldBeCalled();
        $queryBuilder->andWhere(".enabled = :enabled_p1")->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter("enabled_p1", true)->shouldBeCalled();

        $this->applyFilter($conditions, $resourceClass, $queryBuilder);
    }

    function it_should_ignore_an_invalid_condition(ManagerRegistry $managerRegistry, ObjectManager $om, ClassMetadata $classMetadata, LoggerInterface $logger, QueryBuilder $queryBuilder)
    {
        $conditions = ['boolean'=>['enabled'=>'invalid']];
        $resourceClass = Product::class;


        // is property an association false
        $classMetadata->hasAssociation('enabled')->willReturn(false);
        // yes, this is a boolean
        $classMetadata->getTypeOfField('enabled')->willReturn('boolean');
        // is property mapped true
        $classMetadata->hasField('enabled')->willReturn(true);

        $om->getClassMetadata($resourceClass)->willReturn($classMetadata);
        $managerRegistry->getManagerForClass($resourceClass)->willReturn($om);

        $this->beConstructedWith($managerRegistry, $logger);
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyFilter($conditions, $resourceClass, $queryBuilder);
    }
}
