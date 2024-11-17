<?php

namespace App\Event\Enum;

enum UserActivityEvent: string
{
    case SAVE_SUSPECT_ACTIVITY = 'activity.suspect.save';
}
