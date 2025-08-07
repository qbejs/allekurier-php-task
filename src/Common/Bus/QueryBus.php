<?php

declare(strict_types=1);

namespace App\Common\Bus;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QueryBus implements QueryBusInterface
{
    private MessageBusInterface $queryBus;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function dispatch(object $message, array $stamps = []): mixed
    {
        $envelope = $this->queryBus->dispatch($message);

        $handledStamp = $envelope->last(HandledStamp::class);
        if (null === $handledStamp) {
            throw new \RuntimeException('No handler found for message');
        }

        return $handledStamp->getResult();
    }
}
