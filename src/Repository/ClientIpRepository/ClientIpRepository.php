<?php

namespace App\Repository\ClientIpRepository;

use App\Entity\ClientIp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClientIp>
 */
class ClientIpRepository extends ServiceEntityRepository implements ClientIpRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientIp::class);
    }

    /**
     * @inheritDoc
     */
    public function getTrustStatusId(string $clientIp): ?int
    {
        $data = $this->createQueryBuilder('i')
            ->select('i.trust_status as trustStatusId')
            ->where('i.client_ip = :clientIp')
            ->setParameter('clientIp', $clientIp)
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
    public function createClientIp(string $clientIpString, int $trustStatusId): bool
    {
        $em = $this->getEntityManager();
        $clientIp = new ClientIp();
        $clientIp->setClientIp($clientIpString);
        $clientIp->setTrustStatus($trustStatusId);

        try {
            $em->persist($clientIp);
            $em->flush();
        } catch (\Throwable $exception) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function updateTrustStatusById(string $clientIp, int $trustStatusId): bool
    {
        return $this->createQueryBuilder('i')
            ->update()
            ->set('i.trust_status', ':trustStatusId')
            ->where('i.client_ip = :clientIp')
            ->setParameter('trustStatusId', $trustStatusId)
            ->setParameter('clientIp', $clientIp)
            ->getQuery()
            ->execute();
    }
}
