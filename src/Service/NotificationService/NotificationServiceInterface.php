<?php

namespace App\Service\NotificationService;

use App\Service\NotificationService\DTO\Message;
use App\Service\NotificationService\DTO\Sender;

interface NotificationServiceInterface
{
    /**
     * @param \App\Service\NotificationService\DTO\Sender $sender
     * @param \App\Service\NotificationService\DTO\Receiver[] $receivers
     * @param \App\Service\NotificationService\DTO\Message $message
     *
     * @return void
     *
     * @throws \App\Service\NotificationService\Exception\NotificationNotSendException
     */
    public function send(Sender $sender, array $receivers, Message $message): void;
}
