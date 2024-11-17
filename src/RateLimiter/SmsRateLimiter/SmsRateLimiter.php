<?php

namespace App\RateLimiter\SmsRateLimiter;

abstract class SmsRateLimiter
{
    public string $name = 'sms_api';
    public string $policy = 'token_bucket';

    public function __construct(
        public int $limit,
        public string $interval,
        public int $amount,
    ) {}
}
