<?php

namespace App\Service\TrustService;

use App\Repository\BanLogRepository\BanLogRepositoryInterface;
use App\Repository\ClientIpRepository\ClientIpRepositoryInterface;
use App\Repository\PhoneNumberRepository\PhoneNumberRepositoryInterface;
use App\Repository\UserRepository\UserRepositoryInterface;
use App\Service\TrustService\Enum\TrustStatus;

class TrustService implements TrustServiceInterface
{
    /**
     * @param \App\Repository\UserRepository\UserRepositoryInterface $userRepository
     * @param \App\Repository\PhoneNumberRepository\PhoneNumberRepositoryInterface $phoneNumberRepository
     * @param \App\Repository\ClientIpRepository\ClientIpRepositoryInterface $clientIpRepository
     * @param \App\Repository\BanLogRepository\BanLogRepositoryInterface $banLogRepository
     */
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected PhoneNumberRepositoryInterface $phoneNumberRepository,
        protected ClientIpRepositoryInterface $clientIpRepository,
        protected BanLogRepositoryInterface $banLogRepository,
    ) {}

    /**
     * @inheritDoc
     */
    public function generateTrustCode(): string
    {
        return sprintf("%06d", mt_rand(1, 999999));
    }

    /**
     * @inheritDoc
     */
    public function hasUserBannedIdentifiers(int $userId, string $phoneNumber, string $clientIp): bool
    {
        $phoneNumberId = $this->phoneNumberRepository->getIdByPhoneNumber($phoneNumber);
        $bannedIdentifiers = [
            $this->isUserBanned($userId),
            $this->isPhoneNumberBanned($phoneNumberId),
            $this->isClientIpBanned($clientIp),
        ];

        $this->userRepository->getUserTrustStatuses($phoneNumberId);

        return in_array(true, $bannedIdentifiers);
    }

    /**
     * @inheritDoc
     */
    public function getUserBannedIdentifiersString(int $userId, string $phoneNumber, string $clientIp): string
    {
        $identifiers = $this->getUserBannedIdentifiers($userId, $phoneNumber, $clientIp);

        return join(', ', $identifiers);
    }

    /**
     * @inheritDoc
     */
    public function getUserBannedIdentifiers(int $userId, string $phoneNumber, string $clientIp): array
    {
        $identifiers = [];
        $phoneNumberId = $this->phoneNumberRepository->getIdByPhoneNumber($phoneNumber);
        if ($this->isUserBanned($userId)) {
            $identifiers[] = 'user account';
        }
        if ($this->isPhoneNumberBanned($phoneNumberId)) {
            $identifiers[] = 'phone number';
        }
        if ($this->isClientIpBanned($clientIp)) {
            $identifiers[] = 'client ip';
        }

        return $identifiers;
    }

    /**
     * @inheritDoc
     */
    public function isUserBanned(int $userId): bool
    {
        $trustStatus = $this->userRepository->getTrustStatusId($userId);

        if ($trustStatus === TrustStatus::BANNED_ID->value) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isPhoneNumberBanned(int $phoneNumberId): bool
    {
        $trustStatus = $this->phoneNumberRepository->getTrustStatusId($phoneNumberId);

        if ($trustStatus === TrustStatus::BANNED_ID->value) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isClientIpBanned(string $clientIp): bool
    {
        $trustStatus = $this->clientIpRepository->getTrustStatusId($clientIp);

        if ($trustStatus === TrustStatus::BANNED_ID->value) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function banUserById(int $userId, string $reason): void
    {
        $isBanned = $this->userRepository
            ->updateUserTrustStatusById($userId, TrustStatus::BANNED_ID->value);

        if ($isBanned) {
            $this->banLogRepository->createBanLog(
                $userId,
                'user',
                $reason,
                new \DateTime()
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function banUserPhoneNumberById(int $phoneNumberId, string $reason): void
    {
        $isBanned = $this
            ->phoneNumberRepository
            ->updateUserTrustStatusById($phoneNumberId, TrustStatus::BANNED_ID->value);

        if ($isBanned) {
            $this->banLogRepository->createBanLog(
                $phoneNumberId,
                'phone_number',
                $reason,
                new \DateTime()
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function banClientIp(string $clientIp, string $reason): void
    {
        $isBanned = $this->clientIpRepository
            ->updateTrustStatusById($clientIp, TrustStatus::BANNED_ID->value);

        if (!$isBanned) {
            $isBanned = $this->clientIpRepository
                ->createClientIp($clientIp, TrustStatus::BANNED_ID->value);
        }

        if ($isBanned) {
            $this->banLogRepository->createBanLog(
                $clientIp,
                'client_ip',
                $reason,
                new \DateTime()
            );
        }
    }
}
