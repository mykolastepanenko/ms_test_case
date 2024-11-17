<?php

namespace App\RateLimiter;

enum RateLimiterType: string
{
    case PHONE_NUMBER = 'phone number';
    case USER_AGENT = 'user agent';
    case CLIENT_IP = 'client ip';
}
