<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

class PhoneNumberDto
{
    #[Assert\NotBlank(message: 'The "phoneNumber" field is required.')]
    #[AssertPhoneNumber]
    private ?string $phoneNumber = null;

    /**
     * @param string|null $phoneNumber
     */
    public function __construct(?string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     *
     * @return void
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }
}
