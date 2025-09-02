<?php

namespace App\Exceptions;

class AuthorizationException extends BaseException
{
    /**
     * Create a new authorization exception instance
     */
    public function __construct(
        string $message = 'Authorization failed',
        string $userMessage = 'You are not authorized to perform this action',
        string $errorCode = 'AUTHORIZATION_ERROR',
        array $context = [],
        int $code = 403
    ) {
        parent::__construct($message, $userMessage, $errorCode, $context, $code);
    }
}
