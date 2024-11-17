<?php

namespace App\Event\Enum;

enum NotificationEvent: string
{
    case SEND_TRUST_CODE = 'notification.send.trust_code';
}
