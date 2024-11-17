<?php

namespace App\Repository\PhoneNumberRepository;

interface PhoneNumberRepositoryInterface
{
    /**
     * @param string $phoneNumber
     *
     * @return int|null
     */
    public function getIdByPhoneNumber(string $phoneNumber): ?int;

    /**
     * @param int $phoneNumberId
     *
     * @return int|null
     */
    public function getTrustStatusId(int $phoneNumberId): ?int;

    /**
     * @param int $phoneNumberId
     * @param int $trustStatusId
     *
     * @return bool
     */
    public function updateUserTrustStatusById(int $phoneNumberId, int $trustStatusId): bool;
}
