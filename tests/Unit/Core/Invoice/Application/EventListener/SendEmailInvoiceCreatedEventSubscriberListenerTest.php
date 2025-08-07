<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Invoice\Application\EventListener;

use App\Core\Invoice\Application\EventListener\SendEmailInvoiceCreatedEventSubscriberListener;
use App\Core\Invoice\Domain\Event\InvoiceCreatedEvent;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Notification\NotificationInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class SendEmailInvoiceCreatedEventSubscriberListenerTest extends TestCase
{
    private SendEmailInvoiceCreatedEventSubscriberListener $listener;
    private NotificationInterface&MockObject $mailer;
    private TranslatorInterface&MockObject $translator;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(NotificationInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->listener = new SendEmailInvoiceCreatedEventSubscriberListener($this->mailer, $this->translator);
    }

    public function testShouldSendEmailWhenInvoiceCreated(): void
    {
        // Given
        $email = 'test@example.com';
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($email);
        
        $invoice = $this->createMock(Invoice::class);
        $invoice->method('getUser')->willReturn($user);
        
        $event = new InvoiceCreatedEvent($invoice);
        $subject = 'Utworzono fakturę';
        $body = 'Dla twojego konta została wystawiona faktura';

        $this->translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(
                ['emails.invoice_created.subject'],
                ['emails.invoice_created.body']
            )
            ->willReturnOnConsecutiveCalls($subject, $body);

        $this->mailer
            ->expects($this->once())
            ->method('sendEmail')
            ->with($email, $subject, $body);

        // When
        $this->listener->send($event);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }

    public function testShouldSubscribeToInvoiceCreatedEvent(): void
    {
        // Given & When
        $subscribedEvents = SendEmailInvoiceCreatedEventSubscriberListener::getSubscribedEvents();

        // Then
        $this->assertArrayHasKey(InvoiceCreatedEvent::class, $subscribedEvents);
        $this->assertEquals('send', $subscribedEvents[InvoiceCreatedEvent::class]);
    }

    public function testShouldSendEmailWithDifferentInvoice(): void
    {
        // Given
        $email = 'another@example.com';
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($email);
        
        $invoice = $this->createMock(Invoice::class);
        $invoice->method('getUser')->willReturn($user);
        
        $event = new InvoiceCreatedEvent($invoice);
        $subject = 'Utworzono fakturę';
        $body = 'Dla twojego konta została wystawiona faktura';

        $this->translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(
                ['emails.invoice_created.subject'],
                ['emails.invoice_created.body']
            )
            ->willReturnOnConsecutiveCalls($subject, $body);

        $this->mailer
            ->expects($this->once())
            ->method('sendEmail')
            ->with($email, $subject, $body);

        // When
        $this->listener->send($event);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }
}
