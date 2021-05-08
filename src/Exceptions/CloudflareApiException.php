<?php

namespace Calkeo\Ddns\Exceptions;

use Exception;

class CloudflareApiException extends Exception
{
    public static function failedApiRequest(string $message): self
    {
        return new static($message);
    }
}
