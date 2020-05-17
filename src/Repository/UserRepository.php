<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @codeCoverageIgnore
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * (authentication)
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username): ?User
    {
        if (!is_string($username)) {
            return null;
        }

        return $this->findOneByUuid(Uuid::fromString($username));
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->addSelect('r')
            ->addSelect('g')
            ->addSelect('gr')
            ->from(User::class, 'u')
            ->leftJoin('u.roles', 'r')
            ->leftJoin('u.groups', 'g')
            ->leftJoin('g.roles', 'gr')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByUuid(UuidInterface $uuid): ?User
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
