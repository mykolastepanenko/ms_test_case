<?php

namespace App\Service\NotificationService\DTO;

class Receiver
{
    /**
     * @param string $contactInfo
     * @param string|null $name
     */
    public function __construct(protected string $contactInfo, protected ?string $name = null) {}

    public function getContactInfo(): string
    {
        return $this->contactInfo;
    }

    public function setContactInfo(string $contactInfo): void
    {
        $this->contactInfo = $contactInfo;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
