<?php

declare(strict_types=1);

use Sylius\Bundle\CoreBundle\Application\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

if (Kernel::VERSION_ID >= 11200) {
    return;
}

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->extension('swiftmailer', [
        'disable_delivery' => true,
        'logging' => true,
        'spool' => [
            'type' => 'file',
            'path' => '%kernel.cache_dir%/spool',
        ],
    ]);
};
