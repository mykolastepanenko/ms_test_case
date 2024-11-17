<?php

namespace App\Service\ValidationService;

interface ValidationServiceInterface
{
    /**
     * @param mixed $value
     *
     * @return void
     * @throws \Symfony\Component\Validator\Exception\ValidationFailedException
     */
    public function validate(mixed $value): void;
}
