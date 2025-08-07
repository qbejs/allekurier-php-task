<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Invoice\Domain;

use App\Core\Invoice\Domain\Event\InvoiceCanceledEvent;
use App\Core\Invoice\Domain\Event\InvoiceCreatedEvent;
use App\Core\Invoice\Domain\Exception\InvoiceException;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Status\InvoiceStatus;
use App\Core\User\Domain\User;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    public function testShouldCreateInvoiceWithValidAmount(): void
    {
        // Given
        $user = new User('test@example.com');
        $amount = 10000;

        // When
        $invoice = new Invoice($user, $amount);

        // Then
        $this->assertEquals($user, $invoice->getUser());
        $this->assertEquals($amount, $invoice->getAmount());
        $this->assertEquals(InvoiceStatus::NEW, $invoice->getStatus());
        $this->assertNull($invoice->getId());

        $events = $invoice->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(InvoiceCreatedEvent::class, $events[0]);
    }

    public function testShouldThrowExceptionForInvalidAmount(): void
    {
        // Given
        $user = new User('test@example.com');
        $amount = 0;

        // When & Then
        $this->expectException(InvoiceException::class);
        $this->expectExceptionMessage('Kwota faktury musi być większa od 0');

        new Invoice($user, $amount);
    }

    public function testShouldThrowExceptionForNegativeAmount(): void
    {
        // Given
        $user = new User('test@example.com');
        $amount = -1000;

        // When & Then
        $this->expectException(InvoiceException::class);
        $this->expectExceptionMessage('Kwota faktury musi być większa od 0');

        new Invoice($user, $amount);
    }

    public function testShouldCancelInvoice(): void
    {
        // Given
        $user = new User('test@example.com');
        $invoice = new Invoice($user, 10000);

        // Clear initial events
        $invoice->pullEvents();

        // When
        $invoice->cancel();

        // Then
        $this->assertEquals(InvoiceStatus::CANCELED, $invoice->getStatus());

        $events = $invoice->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(InvoiceCanceledEvent::class, $events[0]);
    }

    public function testShouldSetId(): void
    {
        // Given
        $user = new User('test@example.com');
        $invoice = new Invoice($user, 10000);
        $id = 456;

        // When
        $reflection = new \ReflectionClass($invoice);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($invoice, $id);

        // Then
        $this->assertEquals($id, $invoice->getId());
    }

    public function testShouldPullEvents(): void
    {
        // Given
        $user = new User('test@example.com');
        $invoice = new Invoice($user, 10000);

        // When
        $events = $invoice->pullEvents();

        // Then
        $this->assertCount(1, $events);
        $this->assertInstanceOf(InvoiceCreatedEvent::class, $events[0]);

        // Events should be cleared after pulling
        $events = $invoice->pullEvents();
        $this->assertEmpty($events);
    }

    public function testShouldCreateInvoiceWithLargeAmount(): void
    {
        // Given
        $user = new User('test@example.com');
        $amount = 999999;

        // When
        $invoice = new Invoice($user, $amount);

        // Then
        $this->assertEquals($amount, $invoice->getAmount());
        $this->assertEquals(InvoiceStatus::NEW, $invoice->getStatus());
    }
}
