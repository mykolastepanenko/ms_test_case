<?php

namespace App\Service\TrustService;

interface TrustServiceInterface
{
    /**
     * @return string
     */
    public function generateTrustCode(): string;

    /**
     * @param int $userId
     * @param string $phoneNumber
     * @param string $clientIp
     *
     * @return bool
     */
    public function hasUserBannedIdentifiers(int $userId, string $phoneNumber, string $clientIp): bool;

    /**
     * @param int $userId
     * @param string $phoneNumber
     * @param string $clientIp
     *
     * @return string
     */
    public function getUserBannedIdentifiersString(int $userId, string $phoneNumber, string $clientIp): string;

    /**
     * @param int $userId
     * @param string $phoneNumber
     * @param string $clientIp
     *
     * @return array
     */
    public function getUserBannedIdentifiers(int $userId, string $phoneNumber, string $clientIp): array;

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function isUserBanned(int $userId): bool;

    /**
     * @param int $phoneNumberId
     *
     * @return bool
     */
    public function isPhoneNumberBanned(int $phoneNumberId): bool;

    /**
     * @param string $clientIp
     *
     * @return bool
     */
    public function isClientIpBanned(string $clientIp): bool;

    /**
     * @param int $userId
     * @param string $reason
     *
     * @return void
     */
    public function banUserById(int $userId, string $reason): void;

    /**
     * @param int $phoneNumberId
     * @param string $reason
     *
     * @return void
     */
    public function banUserPhoneNumberById(int $phoneNumberId, string $reason): void;

    /**
     * @param string $clientIp
     * @param string $reason
     *
     * @return void
     */
    public function banClientIp(string $clientIp, string $reason): void;
}
