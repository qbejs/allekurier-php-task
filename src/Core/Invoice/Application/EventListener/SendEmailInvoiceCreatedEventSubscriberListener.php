<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\EventListener;

use App\Core\Invoice\Domain\Event\InvoiceCreatedEvent;
use App\Core\Invoice\Domain\Notification\NotificationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SendEmailInvoiceCreatedEventSubscriberListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly NotificationInterface $notification,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function send(InvoiceCreatedEvent $invoiceCreatedEvent): void
    {
        $this->notification->sendEmail(
            $invoiceCreatedEvent->invoice->getUser()->getEmail(),
            $this->translator->trans('emails.invoice_created.subject'),
            $this->translator->trans('emails.invoice_created.body')
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            InvoiceCreatedEvent::class => 'send',
        ];
    }
}
