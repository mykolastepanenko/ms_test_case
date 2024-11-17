<?php

namespace App\EventListener;

use App\DTO\PhoneNumberDto;
use App\Event\Enum\UserActivityEvent;
use App\Event\SuspectActivityEvent;
use App\RateLimiter\RateLimiterType;
use App\Repository\UserRepository\UserRepositoryInterface;
use App\Service\SuspectActivityCounterService\ClientIpSuspectActivityCounterService;
use App\Service\SuspectActivityCounterService\PhoneNumberSuspectActivityCounterService;
use App\Service\SuspectActivityCounterService\SuspectActivityCounterServiceInterface;
use App\Service\SuspectActivityCounterService\UserAgentSuspectActivityCounterService;
use App\Service\TrustService\Attribute\DetectBannedUser;
use App\Service\TrustService\Enum\TrustActionTrigger;
use App\Service\TrustService\TrustServiceInterface;
use App\Service\ValidationService\ValidationServiceInterface;
use Predis\Client as RedisClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class SuspectUserActivitySubscriber implements EventSubscriberInterface
{
    private SuspectActivityEvent $event;
    private SuspectActivityCounterServiceInterface $activityCounterService;

    /**
     * @param \Predis\Client $redis
     * @param \App\Repository\UserRepository\UserRepositoryInterface $userRepository
     * @param \App\Service\TrustService\TrustServiceInterface $trustService
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param \App\Service\ValidationService\ValidationServiceInterface $validationService
     */
    public function __construct(
        private RedisClient                $redis,
        private UserRepositoryInterface    $userRepository,
        private TrustServiceInterface      $trustService,
        private SerializerInterface        $serializer,
        private ValidationServiceInterface $validationService,
    ) {}

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserActivityEvent::SAVE_SUSPECT_ACTIVITY->value => 'onSaveSuspectActivity',
            KernelEvents::CONTROLLER_ARGUMENTS => ['onDetectBannedUser', 100],
        ];
    }

    /**
     * @param \App\Event\SuspectActivityEvent $event
     *
     * @return void
     */
    public function onSaveSuspectActivity(SuspectActivityEvent $event): void
    {
        $this->event = $event;
        $phoneNumber = $event->getPhoneNumberDto()->getPhoneNumber();
        $reasonType = $this->event->getReasonType();
        $reasonTypeValue = $event->getReasonTypeValue();
        $this->activityCounterService = $this->getActivityCounterService($reasonType, $reasonTypeValue);

        $suspectActionCount = $this->activityCounterService->getSuspectActionCount();
        if ($suspectActionCount >= TrustActionTrigger::BAN->value) {
            $this->banUserByIdentifiers(
                $event->getReasonType(), $event->getClientIp(), $phoneNumber
            );
            return;
        }

        $this->activityCounterService->increaseSuspectActivity($suspectActionCount);
    }

    /**
     * @param string $reasonType
     * @param string $typeValue
     *
     * @return \App\Service\SuspectActivityCounterService\SuspectActivityCounterServiceInterface
     */
    private function getActivityCounterService(string $reasonType, string $typeValue): SuspectActivityCounterServiceInterface
    {
        return match($this->event->getReasonType()) {
            RateLimiterType::PHONE_NUMBER->value => new PhoneNumberSuspectActivityCounterService(
                $this->redis, $reasonType, $typeValue
            ),
            RateLimiterType::USER_AGENT->value => new UserAgentSuspectActivityCounterService(
                $this->redis, $reasonType, $typeValue
            ),
            RateLimiterType::CLIENT_IP->value => new ClientIpSuspectActivityCounterService(
                $this->redis, $reasonType, $typeValue
            ),

            default => throw new \InvalidArgumentException('Invalid rate limiter type'),
        };
    }

    /**
     * @param string $reason
     * @param string $clientIp
     * @param string $phoneNumber
     *
     * @return void
     */
    private function banUserByIdentifiers(string $reason, string $clientIp, string $phoneNumber): void
    {
        if ($this->shouldBanClientIp()) {
            $this->trustService->banClientIp($clientIp, $reason);
        }

        ['userId' => $userId, 'phoneNumberId' => $phoneNumberId] = $this->userRepository
            ->getUserIdAndPhoneNumberIdByPhoneNumber($phoneNumber);

        $this->trustService->banUserById($userId, $reason);
        $this->trustService->banUserPhoneNumberById($phoneNumberId, $reason);
    }

    /**
     * @return bool
     */
    private function shouldBanClientIp(): bool
    {
        return $this->event->getReasonType() === RateLimiterType::CLIENT_IP->value;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent $event
     *
     * @return void
     * @throws \ReflectionException
     */
    public function onDetectBannedUser(ControllerArgumentsEvent $event): void
    {
        $controller = $event->getController();
        
        if (!is_array($controller)) {
            return;
        }

        $reflection = new \ReflectionMethod($controller[0], $controller[1]);
        $attributes = $reflection->getAttributes(DetectBannedUser::class);

        foreach ($attributes as $attribute) {
            $request = $event->getRequest();
            $phoneNumberDto = $this->getPhoneNumberDto($request);
            $this->validationService->validate($phoneNumberDto);
            $phoneNumber = $phoneNumberDto->getPhoneNumber();
            $clientIp = $request->getClientIp();

            $isClientIpBanned = $this->trustService->isClientIpBanned($clientIp);
            if ($isClientIpBanned) {
                $this->throwBannedMessage('client ip');
            }

            $userId = $this->userRepository->getUserIdByPhoneNumber($phoneNumber);
            if ($userId === null) {
                $message = 'The user wasn\'t registered.';
                throw new UnprocessableEntityHttpException($message);
            }

            $hasUserBannedIdentifiers = $this->trustService->hasUserBannedIdentifiers(
                $userId, $phoneNumber, $clientIp
            );
            if ($hasUserBannedIdentifiers) {
                $identifiersString = $this->trustService->getUserBannedIdentifiersString(
                    $userId, $phoneNumber, $clientIp
                );
                $this->throwBannedMessage($identifiersString);
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \App\DTO\PhoneNumberDto
     */
    private function getPhoneNumberDto(Request $request): PhoneNumberDto
    {
        if (empty($request->getContent())) {
            throw new UnsupportedMediaTypeHttpException('Unsupported format.');
        }

        /** @var PhoneNumberDto $phoneNumberDto */
        $phoneNumberDto = $this->serializer->deserialize(
            $request->getContent(), PhoneNumberDto::class, 'json'
        );
        $this->validationService->validate($phoneNumberDto);

        return $phoneNumberDto;
    }

    /**
     * @param string $reason
     *
     * @return void
     */
    private function throwBannedMessage(string $reason): void
    {
        $message = "Banned by $reason";
        throw new UnprocessableEntityHttpException($message);
    }
}
