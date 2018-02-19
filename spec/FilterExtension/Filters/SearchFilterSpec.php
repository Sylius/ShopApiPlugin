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
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\ShopApiPlugin\FilterExtension\Filters\SearchFilter;

final class SearchFilterSpec extends ObjectBehavior
{
    function let(ManagerRegistry $managerRegistry, LoggerInterface $logger)
    {
        $this->beConstructedWith($managerRegistry, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SearchFilter::class);
    }

    function it_should_ignore_a_non_search_condition(QueryBuilder $queryBuilder)
    {
        $conditions = ['boolean'=>['translations.name'=>['exact'=>'Banane']]];
        $resourceClass = Product::class;

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyFilter($conditions, $resourceClass, $queryBuilder);
    }

    function it_should_apply_a_search_condition(ManagerRegistry $managerRegistry, ObjectManager $om, ClassMetadata $classMetadata, LoggerInterface $logger, QueryBuilder $queryBuilder)
    {
        $conditions = ['search'=>['translations.name'=>['exact'=>'Banane']]];
        $resourceClass = Product::class;

        // is property an association true
        $classMetadata->hasAssociation('translations')->willReturn(true);
        $classMetadata->hasAssociation('name')->willReturn(false);

        $classMetadata->getAssociationTargetClass('translations')->willReturn(TranslationInterface::class);

        // yes, this is a string
        $classMetadata->getTypeOfField('translations.name')->willReturn('string');

        // is property mapped true
        $classMetadata->hasField('translations.name')->willReturn(true);
        $classMetadata->hasField('name')->willReturn(true);
        $classMetadata->getTypeOfField('name')->willReturn('string');

        $om->getClassMetadata($resourceClass)->willReturn($classMetadata);
        $om->getClassMetadata(TranslationInterface::class)->willReturn($classMetadata);

        $managerRegistry->getManagerForClass(TranslationInterface::class)->willReturn($om);
        $managerRegistry->getManagerForClass($resourceClass)->willReturn($om);

        $this->beConstructedWith($managerRegistry, $logger);
        $queryBuilder->getRootAliases()->shouldBeCalled()->willReturn('o');
        $queryBuilder->getDQLPart('join')->shouldBeCalled()->willReturn([]);
        $queryBuilder->innerJoin('o.translations', 'translations_a1', null, null)->shouldBeCalled();
        $queryBuilder->andWhere('translations_a1.name = :name_p1')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter("name_p1", "Banane")->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyFilter($conditions, $resourceClass, $queryBuilder);
    }

    function it_should_ignore_an_invalid_condition(ManagerRegistry $managerRegistry, ObjectManager $om, ClassMetadata $classMetadata, LoggerInterface $logger, QueryBuilder $queryBuilder)
    {
        $conditions = ['search'=>['translations.name'=>['invalid'=>[]]]];
        $resourceClass = Product::class;

        // is property an association true
        $classMetadata->hasAssociation('translations')->willReturn(true);
        $classMetadata->hasAssociation('name')->willReturn(false);

        $classMetadata->getAssociationTargetClass('translations')->willReturn(TranslationInterface::class);

        // yes, this is a string
        $classMetadata->getTypeOfField('translations.name')->willReturn('string');

        // is property mapped true
        $classMetadata->hasField('translations.name')->willReturn(true);
        $classMetadata->hasField('name')->willReturn(true);

        $om->getClassMetadata($resourceClass)->willReturn($classMetadata);
        $om->getClassMetadata(TranslationInterface::class)->willReturn($classMetadata);

        $managerRegistry->getManagerForClass(TranslationInterface::class)->willReturn($om);
        $managerRegistry->getManagerForClass($resourceClass)->willReturn($om);

        $this->beConstructedWith($managerRegistry, $logger);
        $queryBuilder->getRootAliases()->shouldBeCalled()->willReturn('o');
        $queryBuilder->getDQLPart('join')->shouldBeCalled()->willReturn([]);
        $queryBuilder->innerJoin("o.translations", "translations_a1", null, null)->shouldBeCalled();

        $this->applyFilter($conditions, $resourceClass, $queryBuilder);
    }
}
