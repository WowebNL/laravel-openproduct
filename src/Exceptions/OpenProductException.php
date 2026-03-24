<?php

namespace Woweb\Openproduct\Exceptions;

class OpenProductException extends \RuntimeException
{
    public static function requestFailed(int $status, mixed $body = null): self
    {
        $message = 'Open Product API request failed with status ' . $status;

        if ($body !== null) {
            $message .= ': ' . (is_array($body) ? json_encode($body) : $body);
        }

        return new self($message, $status);
    }
}
