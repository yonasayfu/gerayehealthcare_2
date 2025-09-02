<?php

namespace App\Exceptions;

class ValidationException extends BaseException
{
    /**
     * Validation errors
     */
    protected array $errors = [];

    /**
     * Create a new validation exception instance
     */
    public function __construct(
        string $message = 'Validation failed',
        array $errors = [],
        string $userMessage = '',
        string $errorCode = 'VALIDATION_ERROR',
        array $context = [],
        int $code = 422
    ) {
        parent::__construct($message, $userMessage, $errorCode, $context, $code);
        $this->errors = $errors;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Set validation errors
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Add a validation error
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field][] = $message;

        return $this;
    }

    /**
     * Convert exception to array for API responses
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'errors' => $this->getErrors(),
        ]);
    }
}
