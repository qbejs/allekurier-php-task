<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceHandler;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use App\Core\Invoice\Infrastructure\RateLimiter\InvoiceRateLimiter;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateInvoiceHandlerTest extends TestCase
{
    private CreateInvoiceHandler $createInvoiceHandler;
    private InvoiceRepositoryInterface&MockObject $invoiceRepository;
    private UserRepositoryInterface&MockObject $userRepository;
    private TranslatorInterface&MockObject $translator;
    private InvoiceRateLimiter&MockObject $rateLimiter;

    protected function setUp(): void
    {
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->rateLimiter = $this->createMock(InvoiceRateLimiter::class);

        $this->createInvoiceHandler = new CreateInvoiceHandler(
            $this->invoiceRepository,
            $this->userRepository,
            $this->translator,
            $this->rateLimiter
        );
    }

    public function testShouldCreateInvoiceForActiveUser(): void
    {
        // Given
        $email = 'test@example.com';
        $amount = 10000;
        $activeUser = $this->createMock(User::class);

        $activeUser->method('isActive')->willReturn(true);

        $this->userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn($activeUser);

        $this->rateLimiter
            ->expects($this->once())
            ->method('canCreateInvoice')
            ->with($email)
            ->willReturn(true);

        $this->rateLimiter
            ->expects($this->once())
            ->method('incrementInvoiceCount')
            ->with($email);

        $this->invoiceRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Invoice::class));

        $this->invoiceRepository
            ->expects($this->once())
            ->method('flush');

        // When
        $this->createInvoiceHandler->__invoke(new CreateInvoiceCommand($email, $amount));
    }

    public function testShouldThrowExceptionForInactiveUser(): void
    {
        // Given
        $email = 'test@example.com';
        $amount = 10000;
        $inactiveUser = $this->createMock(User::class);
        $errorMessage = 'Nie można utworzyć faktury dla nieaktywnego użytkownika';

        $inactiveUser->method('isActive')->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn($inactiveUser);

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('errors.cannot_create_invoice_for_inactive_user')
            ->willReturn($errorMessage);

        $this->rateLimiter
            ->expects($this->never())
            ->method('canCreateInvoice');

        $this->invoiceRepository
            ->expects($this->never())
            ->method('save');

        $this->invoiceRepository
            ->expects($this->never())
            ->method('flush');

        // When & Then
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage($errorMessage);

        $this->createInvoiceHandler->__invoke(new CreateInvoiceCommand($email, $amount));
    }

    public function testShouldThrowExceptionForRateLimitExceeded(): void
    {
        // Given
        $email = 'test@example.com';
        $amount = 10000;
        $activeUser = $this->createMock(User::class);
        $errorMessage = 'Przekroczono limit tworzenia faktur. Pozostało 0 faktur w tej godzinie.';

        $activeUser->method('isActive')->willReturn(true);

        $this->userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn($activeUser);

        $this->rateLimiter
            ->expects($this->once())
            ->method('canCreateInvoice')
            ->with($email)
            ->willReturn(false);

        $this->rateLimiter
            ->expects($this->once())
            ->method('getRemainingInvoices')
            ->with($email)
            ->willReturn(0);

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('errors.rate_limit_exceeded', ['%remaining%' => 0])
            ->willReturn($errorMessage);

        $this->invoiceRepository
            ->expects($this->never())
            ->method('save');

        $this->invoiceRepository
            ->expects($this->never())
            ->method('flush');

        // When & Then
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage($errorMessage);

        $this->createInvoiceHandler->__invoke(new CreateInvoiceCommand($email, $amount));
    }
}
