<?php

namespace App\Repository\PhoneNumberRepository;

use App\Entity\PhoneNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PhoneNumber>
 */
class PhoneNumberRepository extends ServiceEntityRepository implements PhoneNumberRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoneNumber::class);
    }

    /**
     * @inheritDoc
     */
    public function getIdByPhoneNumber(string $phoneNumber): ?int
    {
        $data = $this
            ->createQueryBuilder('p')
            ->select('p.id AS phoneNumberId')
            ->where('p.phone_number = :phoneNumber')
            ->setParameter('phoneNumber', $phoneNumber)
            ->getQuery()
            ->getOneOrNullResult();
        
        if ($data === null) {
            return null;
        }

        return $data['phoneNumberId'];
    }

    /**
     * @inheritDoc
     */
    public function getTrustStatusId(int $phoneNumberId): ?int
    {
        $data = $this->createQueryBuilder('p')
            ->select('p.trust_status as trustStatusId')
            ->where('p.id = :phoneNumberId')
            ->setParameter('phoneNumberId', $phoneNumberId)
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
    public function updateUserTrustStatusById(int $phoneNumberId, int $trustStatusId): bool
    {
        return $this->createQueryBuilder('p')
            ->update()
            ->set('p.trust_status', ':trustStatus')
            ->where('p.id = :phoneNumberId')
            ->setParameter('trustStatus', $trustStatusId)
            ->setParameter('phoneNumberId', $phoneNumberId)
            ->getQuery()
            ->execute();
    }
}
