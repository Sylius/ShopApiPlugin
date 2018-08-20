<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class CompleteOrder
{
    /** @var string */
    private $orderToken;

    /** @var string */
    private $email;

    /** @var string|null */
    private $notes;

    /**
     * @param string $orderToken
     * @param string $email
     * @param string|null $notes
     */
    public function __construct($orderToken, $email, $notes = null)
    {
        Assert::string($orderToken);
        Assert::string($email);
        Assert::nullOrString($notes);

        $this->orderToken = $orderToken;
        $this->email = $email;
        $this->notes = $notes;
    }

    public function orderToken()
    {
        return $this->orderToken;
    }

    public function email()
    {
        return $this->email;
    }

    public function notes()
    {
        return $this->notes;
    }
}
