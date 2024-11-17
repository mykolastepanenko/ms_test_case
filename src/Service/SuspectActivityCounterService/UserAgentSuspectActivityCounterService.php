<?php

namespace App\Service\SuspectActivityCounterService;

class UserAgentSuspectActivityCounterService extends SuspectActivityCounterService implements SuspectActivityCounterServiceInterface
{
    /**
     * @inheritDoc
     */
    protected function getKeyTemplate(): string
    {
        return 'activity:suspect:user_agent';
    }
}
