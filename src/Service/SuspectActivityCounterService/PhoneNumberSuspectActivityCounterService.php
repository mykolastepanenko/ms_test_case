<?php

namespace App\Service\SuspectActivityCounterService;

class PhoneNumberSuspectActivityCounterService extends SuspectActivityCounterService implements SuspectActivityCounterServiceInterface
{
    /**
     * @inheritDoc
     */
    protected function getKeyTemplate(): string
    {
        return 'activity:suspect:phone_number';
    }
}
