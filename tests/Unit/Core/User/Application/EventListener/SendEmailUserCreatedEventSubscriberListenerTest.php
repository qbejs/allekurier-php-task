<?php

declare(strict_types=1);

namespace Tests\Unit\Core\User\Application\EventListener;

use App\Common\Mailer\MailerInterface;
use App\Core\User\Application\EventListener\SendEmailUserCreatedEventSubscriberListener;
use App\Core\User\Domain\Event\UserCreatedEvent;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class SendEmailUserCreatedEventSubscriberListenerTest extends TestCase
{
    private SendEmailUserCreatedEventSubscriberListener $listener;
    private MailerInterface&MockObject $mailer;
    private TranslatorInterface&MockObject $translator;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->listener = new SendEmailUserCreatedEventSubscriberListener($this->mailer, $this->translator);
    }

    public function testShouldSendEmailWhenUserCreated(): void
    {
        // Given
        $email = 'test@example.com';
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($email);
        
        $event = new UserCreatedEvent($user);
        $subject = 'Zarejestrowano konto w systemie';
        $body = 'Zarejestrowano konto w systemie. Aktywacja konta trwa do 24h';

        $this->translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(
                ['emails.user_created.subject'],
                ['emails.user_created.body']
            )
            ->willReturnOnConsecutiveCalls($subject, $body);

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($email, $subject, $body);

        // When
        $this->listener->onUserCreated($event);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }

    public function testShouldSubscribeToUserCreatedEvent(): void
    {
        // Given & When
        $subscribedEvents = SendEmailUserCreatedEventSubscriberListener::getSubscribedEvents();

        // Then
        $this->assertArrayHasKey(UserCreatedEvent::class, $subscribedEvents);
        $this->assertEquals('onUserCreated', $subscribedEvents[UserCreatedEvent::class]);
    }

    public function testShouldSendEmailWithDifferentUser(): void
    {
        // Given
        $email = 'another@example.com';
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($email);
        
        $event = new UserCreatedEvent($user);
        $subject = 'Zarejestrowano konto w systemie';
        $body = 'Zarejestrowano konto w systemie. Aktywacja konta trwa do 24h';

        $this->translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(
                ['emails.user_created.subject'],
                ['emails.user_created.body']
            )
            ->willReturnOnConsecutiveCalls($subject, $body);

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($email, $subject, $body);

        // When
        $this->listener->onUserCreated($event);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }
}
