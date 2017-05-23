<?php

namespace Sylius\ShopApiPlugin\View;

class ValidationErrorView
{
    /**
     * @var int
     */
    public $code;

    /**
     * @var string
     */
    public $message;

    /**
     * @var array
     */
    public $errors = [];
}
