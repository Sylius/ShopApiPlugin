<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Utils;

use Symfony\Component\Filesystem\Filesystem;

trait PurgePooledMessagesTrait
{
    private static ?string $poolDirectory = null;

    /**
     * @before
     */
    public function purgePooledMessages(): void
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->getContainer()->get('filesystem');

        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir') . '/pools');
    }
}
