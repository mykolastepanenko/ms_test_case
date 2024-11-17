<?php

namespace App\EventListener\RateLimit;

use App\RateLimiter\RateLimiterType;
use App\RateLimiter\SmsRateLimiter\PhoneNumberRateLimiter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PhoneNumberLimitListener extends RateLimiterListener
{
    /**
     * @inheritDoc
     */
    protected string $rateLimiterClass = PhoneNumberRateLimiter::class;

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

        return $phoneNumberDto->getPhoneNumber();
    }

    /**
     * @inheritDoc
     */
    protected function getRateLimiterType(): string
    {
        return RateLimiterType::PHONE_NUMBER->value;
    }
}
