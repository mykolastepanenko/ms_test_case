<?php

namespace App\Controller;

use App\DTO\PhoneNumberDto;
use App\Event\Enum\NotificationEvent;
use App\Event\SendTrustCodeEvent;
use App\RateLimiter\SmsRateLimiter\ClientIpRateLimiter;
use App\RateLimiter\SmsRateLimiter\PhoneNumberRateLimiter;
use App\RateLimiter\SmsRateLimiter\UserAgentRateLimiter;
use App\Service\NotificationService\DTO\Receiver;
use App\Service\TrustService\Attribute\DetectBannedUser;
use App\Service\TrustService\TrustServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/auth')]
class AuthController extends AbstractController
{
    /**
     * @param \App\Service\TrustService\TrustServiceInterface $trustService
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        private TrustServiceInterface $trustService,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * @param \App\DTO\PhoneNumberDto $phoneNumber
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/confirmation/sms', name: 'confirmation_sms', methods: ['POST'])]
    #[DetectBannedUser]
    #[PhoneNumberRateLimiter(limit: 1, interval: '1 minute', amount: 1)]
    #[UserAgentRateLimiter(limit: 10, interval: '1 minute', amount: 10)]
    #[ClientIpRateLimiter(limit: 100, interval: '1 minute', amount: 100)]
    public function confirmSms(#[MapRequestPayload] PhoneNumberDto $phoneNumber): JsonResponse
    {
        $trustCode = $this->trustService->generateTrustCode();
        $receiver = new Receiver($phoneNumber->getPhoneNumber());
        $event = new SendTrustCodeEvent($trustCode, $receiver);
        $this->eventDispatcher->dispatch($event, NotificationEvent::SEND_TRUST_CODE->value);

        return $this->json([
            'message' => 'We will send the SMS code as soon as possible.',
        ]);
    }
}
