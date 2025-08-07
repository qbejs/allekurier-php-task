<?php

declare(strict_types=1);

namespace App\Core\Invoice\Infrastructure\RateLimiter;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class InvoiceRateLimiter
{
    private const RATE_LIMIT_PREFIX = 'rate_limit_invoice_';
    private const MAX_INVOICES_PER_HOUR = 10;
    private const RATE_LIMIT_TTL = 3600; // 1 hour

    public function __construct(
        private readonly CacheInterface $cache
    ) {
    }

    public function canCreateInvoice(string $email): bool
    {
        $cacheKey = self::RATE_LIMIT_PREFIX.hash('sha256', $email);

        return $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(self::RATE_LIMIT_TTL);

            return 0; // Start with 0 invoices
        }) < self::MAX_INVOICES_PER_HOUR;
    }

    public function incrementInvoiceCount(string $email): void
    {
        $cacheKey = self::RATE_LIMIT_PREFIX.hash('sha256', $email);

        $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(self::RATE_LIMIT_TTL);
            $currentCount = $item->get() ?? 0;
            $item->set($currentCount + 1);

            return $currentCount + 1;
        });
    }

    public function getRemainingInvoices(string $email): int
    {
        $cacheKey = self::RATE_LIMIT_PREFIX.hash('sha256', $email);
        $currentCount = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(self::RATE_LIMIT_TTL);

            return 0;
        });

        return max(0, self::MAX_INVOICES_PER_HOUR - $currentCount);
    }

    public function resetLimit(string $email): void
    {
        $cacheKey = self::RATE_LIMIT_PREFIX.hash('sha256', $email);
        $this->cache->delete($cacheKey);
    }
}
