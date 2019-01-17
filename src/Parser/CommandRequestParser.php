<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Parser;

use Sylius\ShopApiPlugin\Exception\CannotParseCommand;
use Symfony\Component\HttpFoundation\Request;

final class CommandRequestParser implements CommandRequestParserInterface
{
    /** @var array */
    private $commandRequestsMap = [];

    public function __construct(array $commandRequestsMap)
    {
        $this->commandRequestsMap = $commandRequestsMap;
    }

    public function parse(Request $request, string $commandName): object
    {
        if (!isset($this->commandRequestsMap[$commandName])) {
            throw CannotParseCommand::withCommandName($commandName);
        }

        return new $this->commandRequestsMap[$commandName]($request);
    }
}
