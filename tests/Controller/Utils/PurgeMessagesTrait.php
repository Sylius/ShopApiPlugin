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

trait PurgeMessagesTrait
{
    private static ?string $poolDirectory = null;

    use MailerAssertionsTrait;

    /**
     * @before
     */
    public function purgeMessages(): void
    {
        $this->setUpClient();

        /** @var Filesystem $filesystem */
        $filesystem = self::$clientContainer->get('filesystem');

        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir') . '/pools');
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir') . '/spool');
    }
}
