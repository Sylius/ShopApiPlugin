<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ShopApiPlugin extends Bundle
{
    use SyliusPluginTrait;
}
