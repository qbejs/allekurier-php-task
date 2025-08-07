<?php

declare(strict_types=1);

namespace App\Core\Invoice\Domain\Notification;

interface NotificationInterface
{
    public function sendEmail(string $recipient, string $subject, string $message): void;
}
