<?php

namespace App\Service\SuspectActivityCounterService;

class ClientIpSuspectActivityCounterService extends SuspectActivityCounterService implements SuspectActivityCounterServiceInterface
{
    /**
     * @inheritDoc
     */
    protected function getKeyTemplate(): string
    {
        return 'activity:suspect:client_ip';
    }
}
