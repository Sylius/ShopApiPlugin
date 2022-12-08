<?php

declare(strict_types=1);

use Sylius\Bundle\CoreBundle\Application\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

if (Kernel::VERSION_ID >= 11200) {
    return;
}

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->import(__DIR__ . '/../test/swiftmailer.php');
};
