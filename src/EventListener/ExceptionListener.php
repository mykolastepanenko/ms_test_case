<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ExceptionListener
{
    private ExceptionEvent $event;

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $this->event = $event;
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $exception = $exception->getPrevious();
            if ($exception === null) {
                $exception = $event->getThrowable();
            }
        }

        $response = new JsonResponse(
            ['errors' => [$exception->getMessage()]],
            $exception->getStatusCode(),
        );

        if ($exception instanceof ValidationFailedException) {
            $errors = [];

            foreach ($exception->getViolations() as $violation) {
                $errors[] = $violation->getMessage();
            }

            $response = new JsonResponse(
                ['errors' => $errors],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->event->setResponse($response);
    }
}
