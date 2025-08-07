<?php

declare(strict_types=1);

namespace App\Common\Mailer;

interface MailerInterface
{
    public function send(string $recipient, string $subject, string $message): void;
}
