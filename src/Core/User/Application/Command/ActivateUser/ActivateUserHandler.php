<?php

declare(strict_types=1);

namespace App\Core\User\Application\Command\ActivateUser;

use App\Core\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ActivateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(ActivateUserCommand $activateUserCommand): void
    {
        $user = $this->userRepository->getByEmail($activateUserCommand->email);
        $user->activate();

        $this->userRepository->save($user);
        $this->userRepository->flush();
    }
}
