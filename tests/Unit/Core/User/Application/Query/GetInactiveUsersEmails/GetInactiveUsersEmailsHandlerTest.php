<?php

declare(strict_types=1);

namespace Tests\Unit\Core\User\Application\Query\GetInactiveUsersEmails;

use App\Core\User\Application\Query\GetInactiveUsersEmails\GetInactiveUsersEmailsHandler;
use App\Core\User\Application\Query\GetInactiveUsersEmails\GetInactiveUsersEmailsQuery;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetInactiveUsersEmailsHandlerTest extends TestCase
{
    private GetInactiveUsersEmailsHandler $handler;
    private UserRepositoryInterface&MockObject $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->handler = new GetInactiveUsersEmailsHandler($this->userRepository);
    }

    public function testShouldReturnInactiveUsersEmails(): void
    {
        // Given
        $query = new GetInactiveUsersEmailsQuery();
        $expectedEmails = ['user1@example.com', 'user2@example.com', 'user3@example.com'];

        $this->userRepository
            ->expects($this->once())
            ->method('getInactiveUsersEmails')
            ->willReturn($expectedEmails);

        // When
        $result = $this->handler->__invoke($query);

        // Then
        $this->assertEquals($expectedEmails, $result);
        $this->assertCount(3, $result);
    }

    public function testShouldReturnEmptyArrayWhenNoInactiveUsers(): void
    {
        // Given
        $query = new GetInactiveUsersEmailsQuery();

        $this->userRepository
            ->expects($this->once())
            ->method('getInactiveUsersEmails')
            ->willReturn([]);

        // When
        $result = $this->handler->__invoke($query);

        // Then
        $this->assertEmpty($result);
    }

    public function testShouldReturnSingleEmail(): void
    {
        // Given
        $query = new GetInactiveUsersEmailsQuery();
        $expectedEmails = ['single@example.com'];

        $this->userRepository
            ->expects($this->once())
            ->method('getInactiveUsersEmails')
            ->willReturn($expectedEmails);

        // When
        $result = $this->handler->__invoke($query);

        // Then
        $this->assertEquals($expectedEmails, $result);
        $this->assertCount(1, $result);
    }
}
