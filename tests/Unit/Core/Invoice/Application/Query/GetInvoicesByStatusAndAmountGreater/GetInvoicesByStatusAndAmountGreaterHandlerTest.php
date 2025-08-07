<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater;

use App\Core\Invoice\Application\DTO\InvoiceDTO;
use App\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater\GetInvoicesByStatusAndAmountGreaterHandler;
use App\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater\GetInvoicesByStatusAndAmountGreaterQuery;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use App\Core\Invoice\Domain\Status\InvoiceStatus;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetInvoicesByStatusAndAmountGreaterHandlerTest extends TestCase
{
    private GetInvoicesByStatusAndAmountGreaterHandler $handler;
    private InvoiceRepositoryInterface&MockObject $invoiceRepository;

    protected function setUp(): void
    {
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $this->handler = new GetInvoicesByStatusAndAmountGreaterHandler($this->invoiceRepository);
    }

    public function testShouldReturnInvoicesForGivenStatusAndAmount(): void
    {
        // Given
        $status = 'new';
        $amount = 10000;
        $query = new GetInvoicesByStatusAndAmountGreaterQuery($status, $amount);

        $user1 = $this->createMock(User::class);
        $user1->method('getEmail')->willReturn('user1@example.com');

        $user2 = $this->createMock(User::class);
        $user2->method('getEmail')->willReturn('user2@example.com');

        $invoice1 = $this->createMock(Invoice::class);
        $invoice1->method('getId')->willReturn(1);
        $invoice1->method('getUser')->willReturn($user1);
        $invoice1->method('getAmount')->willReturn(15000);

        $invoice2 = $this->createMock(Invoice::class);
        $invoice2->method('getId')->willReturn(2);
        $invoice2->method('getUser')->willReturn($user2);
        $invoice2->method('getAmount')->willReturn(20000);

        $invoices = [$invoice1, $invoice2];

        $this->invoiceRepository
            ->expects($this->once())
            ->method('getInvoicesWithGreaterAmountAndStatus')
            ->with($amount, InvoiceStatus::NEW)
            ->willReturn($invoices);

        // When
        $result = $this->handler->__invoke($query);

        // Then
        $this->assertCount(2, $result);
        $this->assertInstanceOf(InvoiceDTO::class, $result[0]);
        $this->assertInstanceOf(InvoiceDTO::class, $result[1]);
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals('user1@example.com', $result[0]->email);
        $this->assertEquals(15000, $result[0]->amount);
        $this->assertEquals(2, $result[1]->id);
        $this->assertEquals('user2@example.com', $result[1]->email);
        $this->assertEquals(20000, $result[1]->amount);
    }

    public function testShouldReturnEmptyArrayWhenNoInvoicesFound(): void
    {
        // Given
        $status = 'paid';
        $amount = 50000;
        $query = new GetInvoicesByStatusAndAmountGreaterQuery($status, $amount);

        $this->invoiceRepository
            ->expects($this->once())
            ->method('getInvoicesWithGreaterAmountAndStatus')
            ->with($amount, InvoiceStatus::PAID)
            ->willReturn([]);

        // When
        $result = $this->handler->__invoke($query);

        // Then
        $this->assertEmpty($result);
    }

    public function testShouldHandleNullInvoiceId(): void
    {
        // Given
        $status = 'new';
        $amount = 10000;
        $query = new GetInvoicesByStatusAndAmountGreaterQuery($status, $amount);

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('user@example.com');

        $invoice = $this->createMock(Invoice::class);
        $invoice->method('getId')->willReturn(null);
        $invoice->method('getUser')->willReturn($user);
        $invoice->method('getAmount')->willReturn(15000);

        $this->invoiceRepository
            ->expects($this->once())
            ->method('getInvoicesWithGreaterAmountAndStatus')
            ->with($amount, InvoiceStatus::NEW)
            ->willReturn([$invoice]);

        // When & Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invoice ID cannot be null');

        $this->handler->__invoke($query);
    }
}
