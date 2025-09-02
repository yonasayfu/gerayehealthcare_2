<?php

namespace App\Exceptions;

class BusinessException extends BaseException
{
    /**
     * Create a new business exception instance
     */
    public function __construct(
        string $message = 'Business rule violation',
        string $userMessage = '',
        string $errorCode = 'BUSINESS_ERROR',
        array $context = [],
        int $code = 422
    ) {
        parent::__construct($message, $userMessage, $errorCode, $context, $code);
    }
}
