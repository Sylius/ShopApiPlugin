<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Utils;

use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mailer\Event\MessageEvents;
use Symfony\Component\Mailer\Test\Constraint;
use Symfony\Component\Mime\RawMessage;

trait MailerAssertionsTrait
{
    private static ContainerInterface $clientContainer;

    /**
     * @before
     */
    public function setUpClient(): void
    {
        parent::setUpClient();
        self::$clientContainer = $this->client->getContainer();
    }

    public static function assertEmailCount(int $count, string $transport = null, string $message = ''): void
    {
        self::assertThat(self::getMessageMailerEvents(), new Constraint\EmailCount($count, $transport), $message);
    }

    /**
     * @return RawMessage[]
     */
    public static function getMailerMessages(string $transport = null): array
    {
        return self::getMessageMailerEvents()->getMessages($transport);
    }

    public static function getMailerMessage(int $index = 0, string $transport = null): ?RawMessage
    {
        return self::getMailerMessages($transport)[$index] ?? null;
    }

    private static function getMessageMailerEvents(): MessageEvents
    {
        if (self::$clientContainer->has('mailer.message_logger_listener')) {
            return self::$clientContainer->get('mailer.message_logger_listener')->getEvents();
        }

        if (self::$clientContainer->has('mailer.logger_message_listener')) {
            return self::$clientContainer->get('mailer.logger_message_listener')->getEvents();
        }

        throw new AssertionFailedError('A client must have Mailer enabled to make email assertions. Did you forget to require symfony/mailer?');
    }
}
