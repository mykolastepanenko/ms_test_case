<?php

namespace App\Event;

use App\Service\NotificationService\DTO\Receiver;

class SendTrustCodeEvent
{
    /**
     * @param string|null $trustCode
     * @param \App\Service\NotificationService\DTO\Receiver|null $receiver
     */
    public function __construct(private ?string $trustCode, private ?Receiver $receiver) {}

    /**
     * @return string|null
     */
    public function getTrustCode(): ?string
    {
        return $this->trustCode;
    }

    /**
     * @param string|null $trustCode
     *
     * @return void
     */
    public function setTrustCode(?string $trustCode): void
    {
        $this->trustCode = $trustCode;
    }

    /**
     * @return \App\Service\NotificationService\DTO\Receiver|null
     */
    public function getReceiver(): ?Receiver
    {
        return $this->receiver;
    }

    /**
     * @param \App\Service\NotificationService\DTO\Receiver|null $receiver
     *
     * @return void
     */
    public function setReceiver(?Receiver $receiver): void
    {
        $this->receiver = $receiver;
    }
}
