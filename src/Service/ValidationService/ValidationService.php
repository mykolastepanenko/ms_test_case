<?php

namespace App\Service\ValidationService;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService implements ValidationServiceInterface
{
    /**
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     */
    public function __construct(protected ValidatorInterface $validator) {}

    /**
     * @inheritDoc
     */
    public function validate(mixed $value): void
    {
        $errors = $this->validator->validate($value);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                throw new UnprocessableEntityHttpException($error->getMessage());
            }
        }
    }
}
