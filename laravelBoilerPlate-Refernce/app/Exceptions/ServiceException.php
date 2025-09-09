<?php

namespace App\Exceptions;

class ServiceException extends BaseException
{
    /**
     * Create a new service exception instance
     *
     * @param string $message
     * @param string $userMessage
     * @param string $errorCode
     * @param array $context
     * @param int $code
     */
    public function __construct(
        string $message = "Service error occurred",
        string $userMessage = "",
        string $errorCode = "SERVICE_ERROR",
        array $context = [],
        int $code = 500
    ) {
        parent::__construct($message, $userMessage, $errorCode, $context, $code);
    }
}
