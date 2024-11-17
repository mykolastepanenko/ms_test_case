<?php

namespace App\Message;

use App\Service\NotificationService\DTO\Message;
use App\Service\NotificationService\DTO\Sender;

final class SmsNotification
{
    /**
     * @param \App\Service\NotificationService\DTO\Sender $sender
     * @param \App\Service\NotificationService\DTO\Receiver[] $receivers
     * @param \App\Service\NotificationService\DTO\Message $message
     */
     public function __construct(
         public Sender  $sender,
         public array   $receivers,
         public Message $message
     ) {
     }
}
