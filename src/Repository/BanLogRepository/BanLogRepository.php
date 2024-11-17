<?php

namespace App\Repository\BanLogRepository;

use App\Entity\BanLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BanLog>
 */
class BanLogRepository extends ServiceEntityRepository implements BanLogRepositoryInterface
{
    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BanLog::class);
    }

    /**
     * @inheritDoc
     */
    public function createBanLog(string $bannedId, string $bannedType, string $reason, \DateTime $bannedAt = null): void
    {
        $entityManager = $this->getEntityManager();

        $banLog = new BanLog();
        $banLog->setBannedId($bannedId);
        $banLog->setBannedType($bannedType);
        $banLog->setReason($reason);
        $banLog->setBannedAt($bannedAt ?? new \DateTime());

        $entityManager->persist($banLog);
        $entityManager->flush();
    }
}
