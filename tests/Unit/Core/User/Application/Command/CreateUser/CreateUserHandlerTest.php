<?php

declare(strict_types=1);

namespace Tests\Unit\Core\User\Application\Command\CreateUser;

use App\Core\User\Application\Command\CreateUser\CreateUserCommand;
use App\Core\User\Application\Command\CreateUser\CreateUserHandler;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateUserHandlerTest extends TestCase
{
    private CreateUserHandler $handler;
    private UserRepositoryInterface&MockObject $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->handler = new CreateUserHandler($this->userRepository);
    }

    public function testShouldCreateUserWithInactiveStatus(): void
    {
        // Given
        $email = 'test@example.com';
        $command = new CreateUserCommand($email);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) use ($email) {
                return $user->getEmail() === $email && ! $user->isActive();
            }));

        $this->userRepository
            ->expects($this->once())
            ->method('flush');

        // When
        $this->handler->__invoke($command);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }

    public function testShouldCreateUserWithDifferentEmail(): void
    {
        // Given
        $email = 'another@example.com';
        $command = new CreateUserCommand($email);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) use ($email) {
                return $user->getEmail() === $email && ! $user->isActive();
            }));

        $this->userRepository
            ->expects($this->once())
            ->method('flush');

        // When
        $this->handler->__invoke($command);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }
}
