<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Invoice\Infrastructure\RateLimiter;

use App\Core\Invoice\Infrastructure\RateLimiter\InvoiceRateLimiter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class InvoiceRateLimiterTest extends TestCase
{
    private InvoiceRateLimiter $rateLimiter;
    private CacheInterface&MockObject $cache;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->rateLimiter = new InvoiceRateLimiter($this->cache);
    }

    public function testShouldAllowInvoiceCreationWhenUnderLimit(): void
    {
        // Given
        $email = 'test@example.com';
        $item = $this->createMock(ItemInterface::class);
        $item->expects($this->once())->method('expiresAfter')->with(3600);
        $item->method('get')->willReturn(5); // 5 invoices created

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) use ($item) {
                return $callback($item);
            });

        // When
        $result = $this->rateLimiter->canCreateInvoice($email);

        // Then
        $this->assertTrue($result);
    }

    public function testShouldAllowInvoiceCreationForNewUser(): void
    {
        // Given
        $email = 'newuser@example.com';
        $item = $this->createMock(ItemInterface::class);
        $item->expects($this->once())->method('expiresAfter')->with(3600);
        $item->method('get')->willReturn(0); // No invoices created

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) use ($item) {
                return $callback($item);
            });

        // When
        $result = $this->rateLimiter->canCreateInvoice($email);

        // Then
        $this->assertTrue($result);
    }

    public function testShouldIncrementInvoiceCount(): void
    {
        // Given
        $email = 'test@example.com';
        $item = $this->createMock(ItemInterface::class);
        $item->expects($this->once())->method('expiresAfter')->with(3600);
        $item->method('get')->willReturn(5);
        $item->expects($this->once())->method('set')->with(6);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) use ($item) {
                return $callback($item);
            });

        // When
        $this->rateLimiter->incrementInvoiceCount($email);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }

    public function testShouldGetRemainingInvoicesForNewUser(): void
    {
        // Given
        $email = 'test@example.com';
        $item = $this->createMock(ItemInterface::class);
        $item->expects($this->once())->method('expiresAfter')->with(3600);
        $item->method('get')->willReturn(0); // No invoices created

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) use ($item) {
                return $callback($item);
            });

        // When
        $result = $this->rateLimiter->getRemainingInvoices($email);

        // Then
        $this->assertEquals(10, $result); // 10 - 0 = 10 remaining
    }

    public function testShouldResetLimit(): void
    {
        // Given
        $email = 'test@example.com';

        $this->cache
            ->expects($this->once())
            ->method('delete')
            ->with($this->stringContains('rate_limit_invoice_'));

        // When
        $this->rateLimiter->resetLimit($email);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }

    public function testShouldHandleNullCount(): void
    {
        // Given
        $email = 'test@example.com';
        $item = $this->createMock(ItemInterface::class);
        $item->expects($this->once())->method('expiresAfter')->with(3600);
        $item->method('get')->willReturn(null); // No count stored

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) use ($item) {
                return $callback($item);
            });

        // When
        $result = $this->rateLimiter->canCreateInvoice($email);

        // Then
        $this->assertTrue($result);
    }
}
