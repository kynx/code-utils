<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use RuntimeException;

use function sprintf;

/**
 * @see \KynxTest\CodeUtils\NormalizerExceptionTest
 */
final class NormalizerException extends RuntimeException
{
    public static function transliterationFailure(string $string, string|false $error): self
    {
        return new self(sprintf(
            "Cannot transliterate '%s': %s",
            $string,
            $error ?: "Unknown error"
        ));
    }

    public static function invalidSuffix(string $suffix): self
    {
        return new self(sprintf(
            "Invalid reserved word suffix '%s': suffix can only contain '[a-zA-Z0-9_\\x80-\\xff]*' characters",
            $suffix
        ));
    }
}
