<?php

namespace App\EventListener\RateLimit;

use App\RateLimiter\RateLimiterType;
use App\RateLimiter\SmsRateLimiter\UserAgentRateLimiter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class UserAgentLimitListener extends RateLimiterListener
{
    /**
     * @inheritDoc
     */
    protected string $rateLimiterClass = UserAgentRateLimiter::class;

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
        $phoneNumberDto = $this->getPhoneNumberDto($request);
        $userAgent = $request->headers->get('User-Agent');

        return $phoneNumberDto->getPhoneNumber() . '|' . $userAgent;
    }

    /**
     * @inheritDoc
     */
    protected function getRateLimiterType(): string
    {
        return RateLimiterType::USER_AGENT->value;
    }
}
