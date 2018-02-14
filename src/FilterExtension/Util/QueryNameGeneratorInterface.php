<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\FilterExtension\Util;

/**
 * @see       https://github.com/api-platform/core for the canonical source repository
 *
 * @copyright Copyright (c) 2015-present Kévin Dunglas
 * @license   https://github.com/api-platform/core/blob/master/LICENSE MIT License
 *
 * @author Amrouche Hamza <hamza.simperfit@gmail.com>
 */
interface QueryNameGeneratorInterface
{
    /**
     * Generates a cacheable alias for DQL join.
     *
     * @param string $association
     *
     * @return string
     */
    public function generateJoinAlias(string $association): string;

    /**
     * Generates a cacheable parameter name for DQL query.
     *
     * @param string $name
     *
     * @return string
     */
    public function generateParameterName(string $name): string;
}
