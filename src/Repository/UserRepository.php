<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use function Symfony\Component\String\u;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findOrCreateOauth(ResourceOwnerInterface $owner): User {
        $user = $this->createQueryBuilder('u')
            ->where('u.githubId = :githubId')
            ->orWhere('u.email = :email')
            ->setParameters([
                'githubId' => $owner->getId(),
                'email' => $owner->getEmail()
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if ($user) {
            if ($user->getGithubId() === null) {
                $user->setGithubId($owner->getId());
                $this->getEntityManager()->flush();
            }
            return $user;
        }
        $user = (new User())
            ->setGithubId($owner->getId())
            ->setEmail($owner->getEmail())
            ->setRoles(['AG_CUSTOMER'])
            ->setIsVerified(true)
            ->setIsActive(true);
        $manager = $this->getEntityManager();
        $manager->persist($user);
        $manager->flush();

        return $user;
    }

    public function findForOauth(string $service, ?string $serviceId, ?string $email): ?User
    {
        if (null === $serviceId || null === $email) {
            return null;
        }

        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere("u.{$service}Id = :serviceId")
            ->setMaxResults(1)
            ->setParameters([
                'email' => $email,
                'serviceId' => $serviceId,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
