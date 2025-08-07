<?php

declare(strict_types=1);

namespace App\Core\User\Application\Command\ActivateUser;

class ActivateUserCommand
{
    public function __construct(
        public readonly string $email
    ) {
    }
}
