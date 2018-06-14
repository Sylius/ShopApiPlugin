<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\FilterExtension\Filters;

use Doctrine\DBAL\Types\Type as DBALType;
use Doctrine\ORM\QueryBuilder;
use Sylius\ShopApiPlugin\Exception\InvalidArgumentException;
use Sylius\ShopApiPlugin\FilterExtension\Util\QueryNameGenerator;

/**
 * Filters the collection by boolean values.
 *
 * Filters collection on equality of boolean properties. The value is specified
 * as one of ( "true" | "false" | "1" | "0" ) in the query.
 *
 * For each property passed, if the resource does not have such property or if
 * the value is not one of ( "true" | "false" | "1" | "0" ) the property is ignored.
 *
 * The condition must be in the format ?filter['boolean'][{property}]={value}
 *
 * @see       https://github.com/api-platform/core for the canonical source repository
 *
 * @copyright Copyright (c) 2015-present Kévin Dunglas
 * @license   https://github.com/api-platform/core/blob/master/LICENSE MIT License
 * @author Grégoire Hébert <gregoire@les-tilleuls.coop>
 * @author Amrouche Hamza <hamza.simperfit@gmail.com>
 * @author Teoh Han Hui <teohhanhui@gmail.com>
 */
class BooleanFilter extends AbstractFilter
{
    /**
     * Determines whether the given property refers to a boolean field.
     *
     * @param string $property
     * @param string $resourceClass
     *
     * @return bool
     */
    protected function isBooleanField(string $property, string $resourceClass): bool
    {
        $propertyParts = $this->splitPropertyParts($property, $resourceClass);
        $metadata = $this->getNestedMetadata($resourceClass, $propertyParts['associations']);

        return DBALType::BOOLEAN === $metadata->getTypeOfField($propertyParts['field']);
    }

    /**
     * Applies the filter.
     *
     * @param array $conditions
     * @param string $resourceClass
     * @param QueryBuilder $queryBuilder
     */
    public function applyFilter(array $conditions, string $resourceClass, QueryBuilder $queryBuilder): void
    {
        if (empty($conditions['boolean'])) {
            return;
        }

        foreach ($conditions['boolean'] as $property => $value) {
            $queryNameGenerator = new  QueryNameGenerator();

            if (
                !$this->isPropertyMapped($property, $resourceClass) ||
                !$this->isBooleanField($property, $resourceClass)
            ) {
                continue;
            }

            if (\in_array($value, [true, 'true', '1'], true)) {
                $value = true;
            } elseif (\in_array($value, [false, 'false', '0'], true)) {
                $value = false;
            } else {
                $this->logger->notice('Invalid filter ignored', [
                    'exception' => new InvalidArgumentException(sprintf('Invalid boolean value for "%s" property, expected one of ( "%s" )', $property, implode('" | "', [
                        'true',
                        'false',
                        '1',
                        '0',
                    ]))),
                ]);

                continue;
            }

            $alias = $queryBuilder->getRootAliases()[0];
            $field = $property;

            if ($this->isPropertyNested($property, $resourceClass)) {
                [$alias, $field] = $this->addJoinsForNestedProperty($property, $alias, $queryBuilder, $queryNameGenerator, $resourceClass);
            }

            $valueParameter = $queryNameGenerator->generateParameterName($field);

            $queryBuilder
                ->andWhere(sprintf('%s.%s = :%s', $alias, $field, $valueParameter))
                ->setParameter($valueParameter, $value);
        }
    }
}
