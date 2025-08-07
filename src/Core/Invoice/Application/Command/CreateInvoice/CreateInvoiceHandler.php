<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use App\Core\Invoice\Infrastructure\RateLimiter\InvoiceRateLimiter;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler]
class CreateInvoiceHandler
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TranslatorInterface $translator,
        private readonly InvoiceRateLimiter $rateLimiter
    ) {
    }

    public function __invoke(CreateInvoiceCommand $createInvoiceCommand): void
    {
        $user = $this->userRepository->getByEmail($createInvoiceCommand->email);

        if (! $user->isActive()) {
            throw new \DomainException(
                $this->translator->trans('errors.cannot_create_invoice_for_inactive_user')
            );
        }

        // Check rate limit
        if (! $this->rateLimiter->canCreateInvoice($createInvoiceCommand->email)) {
            $remaining = $this->rateLimiter->getRemainingInvoices($createInvoiceCommand->email);

            throw new \DomainException(
                $this->translator->trans('errors.rate_limit_exceeded', ['%remaining%' => $remaining])
            );
        }

        $this->invoiceRepository->save(new Invoice($user, $createInvoiceCommand->amount));
        $this->invoiceRepository->flush();

        // Increment rate limit counter
        $this->rateLimiter->incrementInvoiceCount($createInvoiceCommand->email);
    }
}
