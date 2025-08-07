<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater;

class GetInvoicesByStatusAndAmountGreaterQuery
{
    public function __construct(
        public readonly string $status,
        public readonly int $amount
    ) {
    }
}
