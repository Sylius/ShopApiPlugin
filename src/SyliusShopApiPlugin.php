<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusShopApiPlugin extends Bundle
{
    use SyliusPluginTrait;
}
