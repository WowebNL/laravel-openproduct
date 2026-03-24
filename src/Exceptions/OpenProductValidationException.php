<?php

namespace Woweb\Openproduct\Exceptions;

class OpenProductValidationException extends \InvalidArgumentException
{
    public static function fromErrors(array $errors): self
    {
        return new self('Invalid Open Product data: ' . implode(', ', $errors));
    }
}
