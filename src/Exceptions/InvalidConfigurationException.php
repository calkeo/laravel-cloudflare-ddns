<?php

namespace Calkeo\Ddns\Exceptions;

use Exception;

class InvalidConfigurationException extends Exception
{
    public static function invalidData(array $errors): self
    {
        $output = 'Invalid Cloudflare DDNS configuration:';

        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                $output .= "\n{$message}";
            }
        }

        return new static($output);
    }
}
