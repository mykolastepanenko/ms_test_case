<?php

namespace App\Event;

use App\DTO\PhoneNumberDto;

class SuspectActivityEvent
{
    /**
     * @param \App\DTO\PhoneNumberDto|null $phoneNumberDto
     * @param string|null $reasonType
     * @param string|null $reasonTypeValue
     * @param string|null $clientIp
     */
    public function __construct(
        protected ?PhoneNumberDto $phoneNumberDto,
        protected ?string $reasonType,
        protected ?string $reasonTypeValue,
        protected ?string $clientIp,
    ) {}

    /**
     * @return \App\DTO\PhoneNumberDto|null
     */
    public function getPhoneNumberDto(): ?PhoneNumberDto
    {
        return $this->phoneNumberDto;
    }

    /**
     * @param \App\DTO\PhoneNumberDto|null $phoneNumberDto
     *
     * @return void
     */
    public function setPhoneNumberDto(?PhoneNumberDto $phoneNumberDto): void
    {
        $this->phoneNumberDto = $phoneNumberDto;
    }

    /**
     * @return string|null
     */
    public function getReasonType(): ?string
    {
        return $this->reasonType;
    }

    /**
     * @param string|null $reasonType
     *
     * @return void
     */
    public function setReasonType(?string $reasonType): void
    {
        $this->reasonType = $reasonType;
    }

    /**
     * @return string|null
     */
    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    /**
     * @param string|null $clientIp
     *
     * @return void
     */
    public function setClientIp(?string $clientIp): void
    {
        $this->clientIp = $clientIp;
    }

    /**
     * @return string|null
     */
    public function getReasonTypeValue(): ?string
    {
        return $this->reasonTypeValue;
    }

    /**
     * @param string|null $reasonTypeValue
     *
     * @return void
     */
    public function setReasonTypeValue(?string $reasonTypeValue): void
    {
        $this->reasonTypeValue = $reasonTypeValue;
    }
}
