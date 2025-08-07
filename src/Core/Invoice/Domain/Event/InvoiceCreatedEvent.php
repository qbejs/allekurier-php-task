<?php

declare(strict_types=1);

namespace App\Core\Invoice\Domain\Event;

class InvoiceCreatedEvent extends AbstractInvoiceEvent
{
    public function getUserEmail(): string
    {
        return $this->invoice->getUser()->getEmail();
    }
}
