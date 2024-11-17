<?php

namespace App\Service\SuspectActivityCounterService;

use Predis\Client as RedisClient;

abstract class SuspectActivityCounterService implements SuspectActivityCounterServiceInterface
{
    protected string $keyActionCount;
    protected string $keyActionReason;

    /**
     * @return string
     */
    protected abstract function getKeyTemplate(): string;

    /**
     * @param \Predis\Client $redis
     * @param string $reasonType
     * @param string $reasonTypeValue
     */
    public function __construct(
        protected RedisClient $redis,
        protected string $reasonType,
        protected string $reasonTypeValue,
    ) {
        $keyTemplate = $this->getKeyTemplate();
        $this->keyActionCount = "$keyTemplate:$this->reasonTypeValue:count";
        $this->keyActionReason = "$keyTemplate:$this->reasonTypeValue:reason";
    }

    /**
     * @inheritDoc
     */
    public function getSuspectActionCount(): int
    {
        $suspectActionCount = $this->redis->get($this->keyActionCount);
        if ($suspectActionCount === null) {
            $suspectActionCount = 0;
        }

        return $suspectActionCount;
    }

    /**
     * @inheritDoc
     */
    public function increaseSuspectActivity(?int $suspectActionCount): void
    {
        if ($suspectActionCount === null) {
            $suspectActionCount = 0;
        }

        $ttl = $this->getTTL();
        $this->redis->setex($this->keyActionCount, $ttl, $suspectActionCount + 1);
    }

    /**
     * @return int
     */
    protected function getTTL(): int
    {
        return 60; //1 minute
    }
}
