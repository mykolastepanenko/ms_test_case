<?php

namespace App\Repository\UserRepository;

use App\DTO\TrustStatusDto;
use App\DTO\TrustStatusDtoDecorator;
use App\Entity\PhoneNumber;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntzityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, User::class);
    }

    /**
     * @inheritDoc
     */
    public function getUserTrustStatuses(string $phoneNumber): TrustStatusDtoDecorator
    {
        $queryBuilder = $this
            ->createQueryBuilder('u')
            ->select('u.trust_status AS userStatus', 'p.trust_status AS phoneNumberStatus');
        $data = $this
            ->joinPhoneNumber($queryBuilder, $phoneNumber)
            ->getQuery()
            ->getOneOrNullResult();

        if ($data === null) {
            return new TrustStatusDtoDecorator(null);
        }

        $trustStatus = new TrustStatusDto(...$data);

        return new TrustStatusDtoDecorator($trustStatus);
    }

    /**
     * @inheritDoc
     */
    public function getUserIdByPhoneNumber(string $phoneNumber): ?int
    {
        $queryBuilder = $this
            ->createQueryBuilder('u')
            ->select('u.id AS userId');

        $data = $this
            ->joinPhoneNumber($queryBuilder, $phoneNumber)
            ->getQuery()
            ->getOneOrNullResult();

        if ($data === null) {
            return null;
        }

        return $data['userId'];
    }

    /**
     * @inheritDoc
     */
    public function getTrustStatusId(int $userId): ?int
    {
        $data = $this->createQueryBuilder('u')
            ->select('u.trust_status as trustStatusId')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        if ($data === null) {
            return null;
        }

        return $data['trustStatusId'];
    }

    /**
     * @inheritDoc
     */
    public function getUserIdAndPhoneNumberIdByPhoneNumber(string $phoneNumber): ?array
    {
        $queryBuilder = $this
            ->createQueryBuilder('u')
            ->select('u.id AS userId', 'p.id AS phoneNumberId');

        $data = $this
            ->joinPhoneNumber($queryBuilder, $phoneNumber)
            ->getQuery()
            ->getOneOrNullResult();

        if ($data === null) {
            return null;
        }

        return $data;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string $phoneNumber
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function joinPhoneNumber(QueryBuilder $queryBuilder, string $phoneNumber): QueryBuilder
    {
        return $queryBuilder
            ->join(PhoneNumber::class, 'p', Join::WITH, 'u.id = p.user')
            ->andWhere('p.phone_number = :phoneNumber')
            ->setParameter('phoneNumber', $phoneNumber);
    }

    /**
     * @inheritDoc
     */
    public function updateUserTrustStatusById(int $userId, int $trustStatusId): bool
    {
        return $this->createQueryBuilder('u')
            ->update()
            ->set('u.trust_status', ':trustStatus')
            ->where('u.id = :userId')
            ->setParameter('trustStatus', $trustStatusId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }
}
