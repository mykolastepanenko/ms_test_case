<?php

namespace App\Service\NotificationService\DTO;

class Message
{
    /**
     * @param string|null $subject
     * @param string $message
     */
    public function __construct(protected string $message, protected ?string $subject = null) {}

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
    
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }
}
