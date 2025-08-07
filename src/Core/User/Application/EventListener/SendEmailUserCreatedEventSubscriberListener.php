<?php

declare(strict_types=1);

namespace App\Core\User\Application\EventListener;

use App\Common\Mailer\MailerInterface;
use App\Core\User\Domain\Event\UserCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SendEmailUserCreatedEventSubscriberListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'onUserCreated',
        ];
    }

    public function onUserCreated(UserCreatedEvent $userCreatedEvent): void
    {
        $user = $userCreatedEvent->getUser();

        $this->mailer->send(
            $user->getEmail(),
            $this->translator->trans('emails.user_created.subject'),
            $this->translator->trans('emails.user_created.body')
        );
    }
}
