<?php

namespace App\RateLimiter\SmsRateLimiter;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ClientIpRateLimiter extends SmsRateLimiter
{

}
