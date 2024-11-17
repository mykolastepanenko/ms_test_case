<?php

namespace App\DTO;

use App\Service\TrustService\Enum\TrustStatus;
use Symfony\Component\Validator\Constraints as Assert;

class TrustStatusDto
{
    /**
     * @var int|null $userStatus
     * @see TrustStatus::BANNED_ID for magic number
     */
    #[Assert\NotEqualTo(value: 3, message: 'The user has banned status.')]
    private ?int $userStatus;

    /**
     * @var int|null $phoneNumberStatus
     * @see TrustStatus::BANNED_ID for magic number
     */
    #[Assert\NotEqualTo(value: 3, message: 'The phone number has banned status.')]
    private ?int $phoneNumberStatus;

    /**
     * @param int|null $userStatus
     * @param int|null $phoneNumberStatus
     */
    public function __construct(?int $userStatus, ?int $phoneNumberStatus)
    {
        $this->userStatus = $userStatus;
        $this->phoneNumberStatus = $phoneNumberStatus;
    }

    /**
     * @return int|null
     */
    public function getUserStatus(): ?int
    {
        return $this->userStatus;
    }

    /**
     * @param int|null $userStatus
     *
     * @return void
     */
    public function setUserStatus(?int $userStatus): void
    {
        $this->userStatus = $userStatus;
    }

    /**
     * @return int|null
     */
    public function getPhoneNumberStatus(): ?int
    {
        return $this->phoneNumberStatus;
    }

    /**
     * @param int|null $phoneNumberStatus
     *
     * @return void
     */
    public function setPhoneNumberStatus(?int $phoneNumberStatus): void
    {
        $this->phoneNumberStatus = $phoneNumberStatus;
    }
}
