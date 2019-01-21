<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CompleteOrder;
use Symfony\Component\HttpFoundation\Request;

class CompleteOrderRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    /** @var string|null */
    protected $email;

    /** @var string */
    protected $notes;

    public function populateData(Request $request): void
    {
        $this->token = $request->attributes->get('token');
        $this->email = $request->request->get('email');
        $this->notes = $request->request->get('notes');
    }

    public function getCommand(): object
    {
        return new CompleteOrder($this->token, $this->email, $this->notes);
    }
}
