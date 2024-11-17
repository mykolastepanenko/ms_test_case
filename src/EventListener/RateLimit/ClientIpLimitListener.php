<?php

namespace App\EventListener\RateLimit;

use App\RateLimiter\RateLimiterType;
use App\RateLimiter\SmsRateLimiter\ClientIpRateLimiter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ClientIpLimitListener extends RateLimiterListener
{
    /**
     * @inheritDoc
     */
    protected string $rateLimiterClass = ClientIpRateLimiter::class;

    /**
     * @inheritDoc
     */
    #[AsEventListener(event: KernelEvents::CONTROLLER_ARGUMENTS)]
    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        parent::onKernelControllerArguments($event);
    }

    /**
     * @inheritDoc
     */
    protected function getIdentifier(Request $request): string
    {
        return $this->getClientIp($request);
    }

    /**
     * @inheritDoc
     */
    protected function getRateLimiterType(): string
    {
        return RateLimiterType::CLIENT_IP->value;
    }
}
