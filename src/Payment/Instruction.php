<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Payment;

class Instruction
{
    public $method;
    public $type;
    public $content = [];
}
