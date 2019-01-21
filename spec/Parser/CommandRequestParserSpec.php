<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Parser;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Exception\CannotParseCommand;
use Sylius\ShopApiPlugin\Parser\CommandRequestParserInterface;
use Sylius\ShopApiPlugin\Request\CommandRequestInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;

final class CommandRequestParserSpec extends ObjectBehavior
{
    function let(ServiceLocator $commandRequestLocator): void
    {
        $this->beConstructedWith($commandRequestLocator);
    }

    function it_is_command_request(): void
    {
        $this->shouldImplement(CommandRequestParserInterface::class);
    }

    function it_provides_command_request_object_for_command_name(
        ServiceLocator $commandRequestLocator,
        Request $request,
        CommandRequestInterface $commandRequest
    ): void {
        $commandRequestLocator->has('ValidCommand')->willReturn(true);

        $commandRequestLocator->get('ValidCommand')->willReturn($commandRequest);
        $commandRequest->populateData($request)->shouldBeCalled();

        $this->parse($request, 'ValidCommand')->shouldReturn($commandRequest);
    }

    function it_throws_exception_if_command_request_cannot_be_parsed(
        ServiceLocator $commandRequestLocator,
        Request $request
    ): void {
        $commandRequestLocator->has('InvalidCommand')->willReturn(false);

        $this
            ->shouldThrow(CannotParseCommand::withCommandName('InvalidCommand'))
            ->during('parse', [$request, 'InvalidCommand'])
        ;
    }
}
