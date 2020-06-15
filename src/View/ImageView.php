<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

class ImageView
{
    /** @var string */
    public $code;

    /**
     * @var string|null
     */
    public $alt;

    /**
     * @var string|null
     */
    public $title;
    
    /** @var string */
    public $path;

    /** @var string */
    public $cachedPath;
}
