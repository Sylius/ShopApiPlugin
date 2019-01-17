<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Parser;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Exception\CannotParseCommand;
use Sylius\ShopApiPlugin\Parser\CommandRequestParserInterface;
use Sylius\ShopApiPlugin\Request\ChangeItemQuantityRequest;
use Sylius\ShopApiPlugin\Request\PickupCartRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class CommandRequestParserSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'ChangeItemQuantity' => ChangeItemQuantityRequest::class,
            'PickupCart' => PickupCartRequest::class,
        ]);
    }

    function it_is_command_request(): void
    {
        $this->shouldImplement(CommandRequestParserInterface::class);
    }

    function it_provides_command_request_object_for_command_name(Request $request): void
    {
        $request->attributes = new ParameterBag([]);
        $request->request = new ParameterBag([]);

        $this->parse($request, 'ChangeItemQuantity')->shouldHaveType(ChangeItemQuantityRequest::class);
        $this->parse($request, 'PickupCart')->shouldHaveType(PickupCartRequest::class);
    }

    function it_throws_exception_if_command_request_cannot_be_parsed(Request $request): void
    {
        $this
            ->shouldThrow(CannotParseCommand::withCommandName('InvalidCommand'))
            ->during('parse', [$request, 'InvalidCommand'])
        ;
    }
}
