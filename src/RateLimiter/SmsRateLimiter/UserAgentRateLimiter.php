<?php

namespace App\RateLimiter\SmsRateLimiter;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class UserAgentRateLimiter extends SmsRateLimiter
{

}
