<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Parser;

use Sylius\ShopApiPlugin\Exception\CannotParseCommand;
use Sylius\ShopApiPlugin\Request\CommandRequestInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;

final class CommandRequestParser implements CommandRequestParserInterface
{
    /** @var ServiceLocator */
    private $commandRequestLocator;

    public function __construct(ServiceLocator $commandRequestLocator)
    {
        $this->commandRequestLocator = $commandRequestLocator;
    }

    public function parse(Request $request, string $commandName): CommandRequestInterface
    {
        if (!$this->commandRequestLocator->has($commandName)) {
            throw CannotParseCommand::withCommandName($commandName);
        }

        $commandRequest = $this->commandRequestLocator->get($commandName);
        $commandRequest->populateData($request);

        return $commandRequest;
    }
}
