<?php

declare(strict_types=1);

namespace App\Common\Bus;

interface QueryBusInterface
{
    /**
     * Dispatches the given message.
     *
     * @param array<string, mixed> $stamps
     */
    public function dispatch(object $message, array $stamps = []): mixed;
}
