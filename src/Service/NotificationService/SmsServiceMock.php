<?php

namespace App\Service\NotificationService;

use App\Service\NotificationService\DTO\Message;
use App\Service\NotificationService\DTO\Sender;
use App\Service\NotificationService\Exception\NotificationNotSendException;

class SmsServiceMock implements NotificationServiceInterface
{
    protected const NOTIFICATION_NOT_SEND_MESSAGE = 'The SMS was not sent. (event simulation)';

    /**
     * @inheritDoc
     */
    public function send(Sender $sender, array $receivers, Message $message): void
    {
        /** @var \App\Service\NotificationService\DTO\Receiver $receiver */
        foreach ($receivers as $receiver) {
            $this->fakeSendThirdPartyService(
                $sender->getContactInfo(),
                $receiver->getContactInfo(),
                $message->getMessage()
            );
        }
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $message
     *
     * @return void
     * @throws \App\Service\NotificationService\Exception\NotificationNotSendException
     */
    protected function fakeSendThirdPartyService(string $from, string $to, string $message): void
    {
        $this->simulateNotSendExceptionAtRandom();
    }

    /**
     * @return void
     * @throws \App\Service\NotificationService\Exception\NotificationNotSendException
     */
    protected function simulateNotSendExceptionAtRandom(): void
    {
        if (rand(0, 1) === 1) {
            throw new NotificationNotSendException(static::NOTIFICATION_NOT_SEND_MESSAGE);
        }
    }
}
