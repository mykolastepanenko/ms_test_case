<?php

namespace App\Repository\BanLogRepository;

interface BanLogRepositoryInterface
{
    /**
     * @param string $bannedId
     * @param string $bannedType
     * @param string $reason
     * @param \DateTime|null $bannedAt
     *
     * @return void
     */
    public function createBanLog(string $bannedId, string $bannedType, string $reason, \DateTime $bannedAt = null): void;
}
