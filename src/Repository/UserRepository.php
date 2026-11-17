<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Finds a user by username or email (used by the login form).
     */
    public function findOneByUsernameOrEmail(string $identifier): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :id OR u.email = :id')
            ->setParameter('id', $identifier)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Number of users that have the given role stored (e.g. ROLE_STAFF).
     */
    public function countByRole(string $role): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"' . $role . '"%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return User[] users matching the search text and role, A to Z
     */
    public function search(?string $term, ?string $role): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->orderBy('u.username', 'ASC');

        if ($term) {
            $queryBuilder
                ->andWhere('u.username LIKE :term OR u.fullname LIKE :term OR u.email LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }

        if ('ROLE_USER' === $role) {
            $queryBuilder
                ->andWhere('u.roles NOT LIKE :admin AND u.roles NOT LIKE :staff')
                ->setParameter('admin', '%"ROLE_ADMIN"%')
                ->setParameter('staff', '%"ROLE_STAFF"%');
        } elseif ($role) {
            $queryBuilder
                ->andWhere('u.roles LIKE :role')
                ->setParameter('role', '%"' . $role . '"%');
        }

        return $queryBuilder->getQuery()->getResult();
    }
}