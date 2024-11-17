<?php

namespace App\RateLimiter\SmsRateLimiter;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PhoneNumberRateLimiter extends SmsRateLimiter
{

}
