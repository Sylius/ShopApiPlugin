<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\FilterExtension\Util;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * @see       https://github.com/api-platform/core for the canonical source repository
 *
 * @copyright Copyright (c) 2015-present Kévin Dunglas
 * @license   https://github.com/api-platform/core/blob/master/LICENSE MIT License
 *
 * @author    Vincent Chalamon <vincent@les-tilleuls.coop>
 *
 * @internal
 */
final class QueryBuilderHelper
{
    private function __construct()
    {
    }

    /**
     * Adds a join to the queryBuilder if none exists.
     */
    public static function addJoinOnce(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $alias, string $association, string $joinType = null, string $conditionType = null, string $condition = null): string
    {
        $join = self::getExistingJoin($queryBuilder, $alias, $association);

        if (null !== $join) {
            return $join->getAlias();
        }

        $associationAlias = $queryNameGenerator->generateJoinAlias($association);
        $query = "$alias.$association";

        if (Join::LEFT_JOIN === $joinType || QueryChecker::hasLeftJoin($queryBuilder)) {
            $queryBuilder->leftJoin($query, $associationAlias, $conditionType, $condition);
        } else {
            $queryBuilder->innerJoin($query, $associationAlias, $conditionType, $condition);
        }

        return $associationAlias;
    }

    /**
     * Get the existing join from queryBuilder DQL parts.
     *
     * @return Join|null
     */
    private static function getExistingJoin(QueryBuilder $queryBuilder, string $alias, string $association)
    {
        $parts = $queryBuilder->getDQLPart('join');
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (!isset($parts[$rootAlias])) {
            return null;
        }

        foreach ($parts[$rootAlias] as $join) {
            /** @var Join $join */
            if (sprintf('%s.%s', $alias, $association) === $join->getJoin()) {
                return $join;
            }
        }

        return null;
    }
}
