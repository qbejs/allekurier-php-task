<?php

declare(strict_types=1);

namespace App\Core\User\Application\Command\CreateUser;

use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(CreateUserCommand $createUserCommand): void
    {
        $user = new User($createUserCommand->email, false); // false = nieaktywny

        $this->userRepository->save($user);
        $this->userRepository->flush();
    }
}
