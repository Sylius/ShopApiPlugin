<?php

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class CompleteOrder
{
    /**
     * @var string
     */
    private $orderToken;

    /**
     * @var string
     */
    private $email;

    /**
     * @var null|string
     */
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

    /**
     * @return string
     */
    public function orderToken()
    {
        return $this->orderToken;
    }

    /**
     * @return string
     */
    public function email()
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function notes()
    {
        return $this->notes;
    }
}
