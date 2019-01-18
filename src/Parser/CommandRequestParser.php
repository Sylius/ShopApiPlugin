<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Parser;

use Sylius\ShopApiPlugin\Exception\CannotParseCommand;
use Sylius\ShopApiPlugin\Request\CommandRequestInterface;
use Symfony\Component\HttpFoundation\Request;

final class CommandRequestParser implements CommandRequestParserInterface
{
    /** @var array */
    private $commandRequestsMap = [];

    public function __construct(array $commandRequestsMap)
    {
        $this->commandRequestsMap = $commandRequestsMap;
    }

    public function parse(Request $request, string $commandName): CommandRequestInterface
    {
        if (!isset($this->commandRequestsMap[$commandName])) {
            throw CannotParseCommand::withCommandName($commandName);
        }

        /** @var CommandRequestInterface $commandRequest */
        $commandRequest = new $this->commandRequestsMap[$commandName]();
        $commandRequest->populateData($request);

        return $commandRequest;
    }
}
