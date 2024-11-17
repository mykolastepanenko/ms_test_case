<?php

namespace App\Repository\UserRepository;

use App\DTO\TrustStatusDtoDecorator;

interface UserRepositoryInterface
{
    /**
     * @param string $phoneNumber
     *
     * @return \App\DTO\TrustStatusDtoDecorator
     */
    public function getUserTrustStatuses(string $phoneNumber): TrustStatusDtoDecorator;

    /**
     * @param string $phoneNumber
     *
     * @return int|null
     */
    public function getUserIdByPhoneNumber(string $phoneNumber): ?int;

    /**
     * @param string $phoneNumber
     *
     * @return array|null
     */
    public function getUserIdAndPhoneNumberIdByPhoneNumber(string $phoneNumber): ?array;

    /**
     * @param int $userId
     *
     * @return int|null
     */
    public function getTrustStatusId(int $userId): ?int;

    /**
     * @param int $userId
     * @param int $trustStatusId
     *
     * @return bool
     */
    public function updateUserTrustStatusById(int $userId, int $trustStatusId): bool;
}
