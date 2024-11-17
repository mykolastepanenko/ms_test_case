<?php

namespace App\EventListener\RateLimit;

use App\DTO\PhoneNumberDto;
use App\Event\Enum\UserActivityEvent;
use App\Event\SuspectActivityEvent;
use App\RateLimiter\SmsRateLimiter\SmsRateLimiter;
use DateInterval;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\RateLimiter\Policy\Rate;
use Symfony\Component\RateLimiter\Policy\TokenBucketLimiter;
use Symfony\Component\RateLimiter\Storage\CacheStorage;
use Symfony\Component\RateLimiter\Storage\StorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class RateLimiterListener
{
    /**
     * @var \Symfony\Component\RateLimiter\Policy\TokenBucketLimiter $limiter
     */
    protected TokenBucketLimiter $limiter;

    /**
     * @var string $rateLimiterClass
     */
    protected string $rateLimiterClass;

    /**
     * @var \Symfony\Component\RateLimiter\Storage\StorageInterface $storage
     */
    protected StorageInterface $storage;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected abstract function getIdentifier(Request $request): string;

    /**
     * @return string
     * 
     * @see \App\RateLimiter\RateLimiterType
     */
    protected abstract function getRateLimiterType(): string;

    /**
     * @param \Psr\Cache\CacheItemPoolInterface $cache
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     */
    public function __construct(
        protected CacheItemPoolInterface $cache,
        protected EventDispatcherInterface $eventDispatcher,
        protected SerializerInterface $serializer,
        protected ValidatorInterface $validator,
    ) {
        $this->storage = new CacheStorage($cache);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent $event
     *
     * @return void
     */
    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        $reflection = new \ReflectionMethod($controller[0], $controller[1]);
        $attributes = $reflection->getAttributes($this->rateLimiterClass);

        foreach ($attributes as $attribute) {
            /** @var SmsRateLimiter $rateLimit */
            $rateLimit = $attribute->newInstance();

            $rateTime = $this->convertToDateInterval($rateLimit->interval);
            $rate = new Rate($rateTime, $rateLimit->amount);

            $request = $event->getRequest();
            $identifier = $this->getIdentifier($request);
            $limitCount = $this->getRateLimit($rateLimit);
            $this->limiter = $this->createLimiter($identifier, $limitCount, $rate);

            $limit = $this->limiter->consume();

            if (!$limit->isAccepted()) {
                $this->dispatchSuspectEvent($request, $identifier);

                $event->setController(fn() => new JsonResponse([
                    'error' => 'Too many requests',
                    'rate_limiter_type' => $this->getRateLimiterType(),
                    'retry_after' => $limit->getRetryAfter()->format('Y-m-d H:i:s'),
                ], Response::HTTP_TOO_MANY_REQUESTS));
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $reasonTypeValue
     *
     * @return void
     */
    protected function dispatchSuspectEvent(Request $request, string $reasonTypeValue): void
    {
        $phoneNumberDto = $this->getPhoneNumberDto($request);
        $reasonType = $this->getRateLimiterType();
        $clientIp = $this->getClientIp($request);
        $suspectEvent = new SuspectActivityEvent(
            $phoneNumberDto, $reasonType, $clientIp, $reasonTypeValue
        );
        $suspectEvent->setPhoneNumberDto($phoneNumberDto);
        $suspectEvent->setReasonType($reasonType);
        $suspectEvent->setReasonTypeValue($reasonTypeValue);
        $suspectEvent->setClientIp($clientIp);

        $this->eventDispatcher->dispatch($suspectEvent, UserActivityEvent::SAVE_SUSPECT_ACTIVITY->value);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \App\DTO\PhoneNumberDto
     */
    protected function getPhoneNumberDto(Request $request): PhoneNumberDto
    {
        if (empty($request->getContent())) {
            throw new UnsupportedMediaTypeHttpException('Unsupported format.');
        }

        /** @var PhoneNumberDto $phoneNumberDto */
        $phoneNumberDto = $this->serializer->deserialize(
            $request->getContent(), PhoneNumberDto::class, 'json'
        );
        $this->validator->validate($phoneNumberDto);

        return $phoneNumberDto;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function getClientIp(Request $request): string
    {
        return $request->getClientIp();
    }

    /**
     * @param string $identifier
     * @param int $limit
     * @param \Symfony\Component\RateLimiter\Policy\Rate $rate
     *
     * @return \Symfony\Component\RateLimiter\Policy\TokenBucketLimiter
     */
    protected function createLimiter(string $identifier, int $limit, Rate $rate): TokenBucketLimiter
    {
        return new TokenBucketLimiter($identifier, $limit, $rate, $this->storage);
    }

    /**
     * @param \App\RateLimiter\SmsRateLimiter\SmsRateLimiter $limiter
     *
     * @return int
     */
    protected function getRateLimit(SmsRateLimiter $limiter): int
    {
        return $limiter->limit;
    }

    /**
     * @param string $time
     *
     * @return \DateInterval
     */
    private function convertToDateInterval(string $time): DateInterval
    {
        $pattern = '/(\d+)\s*(seconds?|minutes?|hours?|days?)/i';
        $formattedTime = preg_replace_callback($pattern, function ($matches) {
            $number = $matches[1];
            $unit = strtolower($matches[2]);

            return match ($unit) {
                'second', 'seconds' => 'PT' . $number . 'S',
                'minute', 'minutes' => 'PT' . $number . 'M',
                'hour', 'hours' => 'PT' . $number . 'H',
                'day', 'days' => 'P' . $number . 'D',

                default => 'S',
            };
        }, $time);

        return new DateInterval($formattedTime);
    }
}
