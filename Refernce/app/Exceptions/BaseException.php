<?php

namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    /**
     * User-friendly message
     */
    protected string $userMessage;

    /**
     * Error code
     */
    protected string $errorCode;

    /**
     * Additional context data
     */
    protected array $context = [];

    /**
     * Create a new exception instance
     */
    public function __construct(
        string $message = '',
        string $userMessage = '',
        string $errorCode = '',
        array $context = [],
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->userMessage = $userMessage ?: $message;
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    /**
     * Get the user-friendly message
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Get the error code
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get the context data
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set the user-friendly message
     */
    public function setUserMessage(string $message): self
    {
        $this->userMessage = $message;

        return $this;
    }

    /**
     * Set the error code
     */
    public function setErrorCode(string $code): self
    {
        $this->errorCode = $code;

        return $this;
    }

    /**
     * Add context data
     */
    public function addContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    /**
     * Convert exception to array for API responses
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getUserMessage(),
            'error_code' => $this->getErrorCode(),
            'context' => $this->getContext(),
        ];
    }
}
