<?php

declare(strict_types=1);

namespace Tests\Unit\Core\User\Domain;

use App\Core\User\Domain\Event\UserCreatedEvent;
use App\Core\User\Domain\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testShouldCreateUserWithInactiveStatus(): void
    {
        // Given
        $email = 'test@example.com';

        // When
        $user = new User($email);

        // Then
        $this->assertEquals($email, $user->getEmail());
        $this->assertFalse($user->isActive());
        $this->assertNull($user->getId());

        $events = $user->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserCreatedEvent::class, $events[0]);
    }

    public function testShouldCreateUserWithActiveStatus(): void
    {
        // Given
        $email = 'test@example.com';

        // When
        $user = new User($email, true);

        // Then
        $this->assertEquals($email, $user->getEmail());
        $this->assertTrue($user->isActive());
        $this->assertNull($user->getId());
    }

    public function testShouldActivateUser(): void
    {
        // Given
        $user = new User('test@example.com');

        // When
        $user->activate();

        // Then
        $this->assertTrue($user->isActive());
    }

    public function testShouldDeactivateUser(): void
    {
        // Given
        $user = new User('test@example.com', true);

        // When
        $user->deactivate();

        // Then
        $this->assertFalse($user->isActive());
    }

    public function testShouldPullEvents(): void
    {
        // Given
        $user = new User('test@example.com');

        // When
        $events = $user->pullEvents();

        // Then
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserCreatedEvent::class, $events[0]);

        // Events should be cleared after pulling
        $events = $user->pullEvents();
        $this->assertEmpty($events);
    }

    public function testShouldSetId(): void
    {
        // Given
        $user = new User('test@example.com');
        $id = 123;

        // When
        $reflection = new \ReflectionClass($user);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($user, $id);

        // Then
        $this->assertEquals($id, $user->getId());
    }
}
