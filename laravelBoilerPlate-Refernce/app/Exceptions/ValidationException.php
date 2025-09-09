<?php

namespace App\Exceptions;

class ValidationException extends BaseException
{
    /**
     * Validation errors
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Create a new validation exception instance
     *
     * @param string $message
     * @param array $errors
     * @param string $userMessage
     * @param string $errorCode
     * @param array $context
     * @param int $code
     */
    public function __construct(
        string $message = "Validation failed",
        array $errors = [],
        string $userMessage = "",
        string $errorCode = "VALIDATION_ERROR",
        array $context = [],
        int $code = 422
    ) {
        parent::__construct($message, $userMessage, $errorCode, $context, $code);
        $this->errors = $errors;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Set validation errors
     *
     * @param array $errors
     * @return self
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Add a validation error
     *
     * @param string $field
     * @param string $message
     * @return self
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field][] = $message;
        return $this;
    }

    /**
     * Convert exception to array for API responses
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'errors' => $this->getErrors(),
        ]);
    }
}
