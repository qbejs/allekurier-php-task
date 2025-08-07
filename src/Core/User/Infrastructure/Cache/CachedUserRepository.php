<?php

declare(strict_types=1);

namespace App\Core\User\Infrastructure\Cache;

use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedUserRepository implements UserRepositoryInterface
{
    private const CACHE_TTL = 3600; // 1 hour
    private const USER_CACHE_PREFIX = 'user_email_';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CacheInterface $cache
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getByEmail(string $email): User
    {
        $cacheKey = self::USER_CACHE_PREFIX.hash('sha256', $email);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($email) {
            $item->expiresAfter(self::CACHE_TTL);

            $user = $this->entityManager->createQueryBuilder()
                ->select('u')
                ->from(User::class, 'u')
                ->where('u.email = :user_email')
                ->setParameter(':user_email', $email)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if (null === $user) {
                throw new UserNotFoundException('Użytkownik nie istnieje');
            }

            return $user;
        });
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);

        $events = $user->pullEvents();
        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        // Invalidate cache for this user
        $this->invalidateUserCache($user->getEmail());
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @return string[]
     */
    public function getInactiveUsersEmails(): array
    {
        $cacheKey = 'users_inactive_emails';

        return $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_TTL);

            $result = $this->entityManager->createQueryBuilder()
                ->select('u.email')
                ->from(User::class, 'u')
                ->where('u.isActive = :isActive')
                ->setParameter(':isActive', false)
                ->getQuery()
                ->getScalarResult();

            return array_column($result, 'email');
        });
    }

    private function invalidateUserCache(string $email): void
    {
        $cacheKey = self::USER_CACHE_PREFIX.hash('sha256', $email);
        $this->cache->delete($cacheKey);

        // Also invalidate inactive users cache
        $this->cache->delete('users_inactive_emails');
    }
}
