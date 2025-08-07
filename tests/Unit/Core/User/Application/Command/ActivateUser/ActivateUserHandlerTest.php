<?php

declare(strict_types=1);

namespace Tests\Unit\Core\User\Application\Command\ActivateUser;

use App\Core\User\Application\Command\ActivateUser\ActivateUserCommand;
use App\Core\User\Application\Command\ActivateUser\ActivateUserHandler;
use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ActivateUserHandlerTest extends TestCase
{
    private ActivateUserHandler $handler;
    private UserRepositoryInterface&MockObject $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->handler = new ActivateUserHandler($this->userRepository);
    }

    public function testShouldActivateUser(): void
    {
        // Given
        $email = 'test@example.com';
        $command = new ActivateUserCommand($email);

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('activate');

        $this->userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $this->userRepository
            ->expects($this->once())
            ->method('flush');

        // When
        $this->handler->__invoke($command);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }

    public function testShouldThrowExceptionWhenUserNotFound(): void
    {
        // Given
        $email = 'nonexistent@example.com';
        $command = new ActivateUserCommand($email);
        $errorMessage = 'Użytkownik nie istnieje';

        $this->userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willThrowException(new UserNotFoundException($errorMessage));

        $this->userRepository
            ->expects($this->never())
            ->method('save');

        $this->userRepository
            ->expects($this->never())
            ->method('flush');

        // When & Then
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage($errorMessage);

        $this->handler->__invoke($command);
    }

    public function testShouldActivateUserWithDifferentEmail(): void
    {
        // Given
        $email = 'another@example.com';
        $command = new ActivateUserCommand($email);

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('activate');

        $this->userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $this->userRepository
            ->expects($this->once())
            ->method('flush');

        // When
        $this->handler->__invoke($command);

        // Then - no exception should be thrown
        $this->assertTrue(true);
    }
}
