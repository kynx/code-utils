<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use RuntimeException;

use function implode;
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

    /**
     * @param list<string> $validCases
     */
    public static function invalidCase(string $case, array $validCases): self
    {
        return new self(sprintf(
            "Invalid case '%s': expected one of '%s'",
            $case,
            implode("', '", $validCases)
        ));
    }
}
