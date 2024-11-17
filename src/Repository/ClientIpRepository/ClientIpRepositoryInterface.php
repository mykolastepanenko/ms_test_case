<?php

namespace App\Repository\ClientIpRepository;

interface ClientIpRepositoryInterface
{
    /**
     * @param string $clientIp
     *
     * @return int|null
     */
    public function getTrustStatusId(string $clientIp): ?int;

    /**
     * @param string $clientIpString
     * @param int $trustStatusId
     *
     * @return bool
     */
    public function createClientIp(string $clientIpString, int $trustStatusId): bool;

    /**
     * @param string $clientIp
     * @param int $trustStatusId
     *
     * @return bool
     */
    public function updateTrustStatusById(string $clientIp, int $trustStatusId): bool;
}
