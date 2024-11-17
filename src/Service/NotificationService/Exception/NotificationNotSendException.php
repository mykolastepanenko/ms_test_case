<?php

namespace App\Service\NotificationService\Exception;

class NotificationNotSendException extends \Exception
{
    protected $message = 'The notification was not sent.';
}
