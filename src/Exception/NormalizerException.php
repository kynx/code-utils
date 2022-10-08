<?php

declare(strict_types=1);

namespace Kynx\CodeUtils\Exception;

use RuntimeException;

use function implode;
use function sprintf;

final class NormalizerException extends RuntimeException
{
    public static function transliterationFailure(string $string, string|false $error): self
    {
        return new self("Cannot transliterate '$string': " . ($error ?: "Unknown error"));
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
