<?php

namespace App\EventListener;

use App\Event\Enum\NotificationEvent;
use App\Event\SendTrustCodeEvent;
use App\Message\SmsNotification;
use App\Service\NotificationService\DTO\Message;
use App\Service\NotificationService\DTO\Sender;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

final class SendTrustCodeListener
{
    /**
     * @param \Symfony\Component\Messenger\MessageBusInterface $bus
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag
     */
    public function __construct(
        private MessageBusInterface $bus,
        private ParameterBagInterface $parameterBag,
    ) {}

    /**
     * @param \App\Event\SendTrustCodeEvent $event
     *
     * @return void
     * 
     * @see NotificationEvent::SEND_TRUST_CODE for the event
     */
    #[AsEventListener(event: 'notification.send.trust_code')]
    public function sendTrustCodeSms(SendTrustCodeEvent $event): void
    {
        $senderContactInfo = $this->parameterBag->get('notifications')['sms']['sender'];
        $sender = new Sender($senderContactInfo);
        $receiver = $event->getReceiver();
        $trustCode = $event->getTrustCode();

        $messageString = "Your code: $trustCode";
        $message = new Message($messageString);

        $this->bus->dispatch(new SmsNotification(
                $sender,
                [$receiver],
                $message)
        );
    }
}
