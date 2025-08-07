<?php

declare(strict_types=1);

namespace App\Core\User\Application\Query\GetInactiveUsersEmails;

use App\Core\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetInactiveUsersEmailsHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @return string[]
     */
    public function __invoke(GetInactiveUsersEmailsQuery $query): array
    {
        return $this->userRepository->getInactiveUsersEmails();
    }
}
