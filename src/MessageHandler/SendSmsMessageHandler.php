<?php

namespace App\MessageHandler;

use App\Message\SmsNotification;
use App\Service\NotificationService\Exception\NotificationNotSendException;
use App\Service\NotificationService\NotificationServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendSmsMessageHandler
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \App\Service\NotificationService\NotificationServiceInterface $notificationService
     */
    public function __construct(
        private LoggerInterface $logger,
        private NotificationServiceInterface $notificationService
    ) {}

    /**
     * @param \App\Message\SmsNotification $message
     *
     * @return void
     */
    public function __invoke(SmsNotification $message): void
    {
        try {
            $this->logger->info('Sending sms notification...');
            $this->notificationService->send(
                $message->sender, $message->receivers, $message->message
            );
        } catch (NotificationNotSendException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
