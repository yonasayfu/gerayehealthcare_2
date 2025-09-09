<?php

namespace App\Exceptions;

class BusinessException extends BaseException
{
    /**
     * Create a new business exception instance
     *
     * @param string $message
     * @param string $userMessage
     * @param string $errorCode
     * @param array $context
     * @param int $code
     */
    public function __construct(
        string $message = "Business rule violation",
        string $userMessage = "",
        string $errorCode = "BUSINESS_ERROR",
        array $context = [],
        int $code = 422
    ) {
        parent::__construct($message, $userMessage, $errorCode, $context, $code);
    }
}
