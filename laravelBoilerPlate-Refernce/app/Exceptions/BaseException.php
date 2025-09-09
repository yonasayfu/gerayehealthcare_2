<?php

namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    /**
     * User-friendly message
     *
     * @var string
     */
    protected string $userMessage;

    /**
     * Error code
     *
     * @var string
     */
    protected string $errorCode;

    /**
     * Additional context data
     *
     * @var array
     */
    protected array $context = [];

    /**
     * Create a new exception instance
     *
     * @param string $message
     * @param string $userMessage
     * @param string $errorCode
     * @param array $context
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(
        string $message = "",
        string $userMessage = "",
        string $errorCode = "",
        array $context = [],
        int $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->userMessage = $userMessage ?: $message;
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    /**
     * Get the user-friendly message
     *
     * @return string
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Get the error code
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get the context data
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set the user-friendly message
     *
     * @param string $message
     * @return self
     */
    public function setUserMessage(string $message): self
    {
        $this->userMessage = $message;
        return $this;
    }

    /**
     * Set the error code
     *
     * @param string $code
     * @return self
     */
    public function setErrorCode(string $code): self
    {
        $this->errorCode = $code;
        return $this;
    }

    /**
     * Add context data
     *
     * @param array $context
     * @return self
     */
    public function addContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);
        return $this;
    }

    /**
     * Convert exception to array for API responses
     *
     * @return array
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
