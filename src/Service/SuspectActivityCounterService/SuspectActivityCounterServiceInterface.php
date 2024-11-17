<?php

namespace App\Service\SuspectActivityCounterService;

interface SuspectActivityCounterServiceInterface
{
    /**
     * @return int
     */
    public function getSuspectActionCount(): int;

    /**
     * @param int|null $suspectActionCount
     *
     * @return void
     */
    public function increaseSuspectActivity(?int $suspectActionCount): void;
}
