<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\ShopApiPlugin\DependencyInjection\Compiler\FiltersDefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ShopApiPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FiltersDefinitionPass());
    }
}
