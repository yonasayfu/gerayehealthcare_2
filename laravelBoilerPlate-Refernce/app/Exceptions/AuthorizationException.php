<?php

namespace App\Exceptions;

class AuthorizationException extends BaseException
{
    /**
     * Create a new authorization exception instance
     *
     * @param string $message
     * @param string $userMessage
     * @param string $errorCode
     * @param array $context
     * @param int $code
     */
    public function __construct(
        string $message = "Authorization failed",
        string $userMessage = "You are not authorized to perform this action",
        string $errorCode = "AUTHORIZATION_ERROR",
        array $context = [],
        int $code = 403
    ) {
        parent::__construct($message, $userMessage, $errorCode, $context, $code);
    }
}
