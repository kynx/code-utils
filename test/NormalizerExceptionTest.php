<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\NormalizerException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Code\Normalizer\NormalizerException
 */
final class NormalizerExceptionTest extends TestCase
{
    public function testTransliterationFailureWithErrorString(): void
    {
        $string   = "abc";
        $error    = "Foo";
        $expected = "Cannot transliterate '$string': $error";
        $actual   = NormalizerException::transliterationFailure($string, $error);
        self::assertSame($expected, $actual->getMessage());
    }

    public function testTransliterationFailureWithFalseError(): void
    {
        $string   = "abc";
        $error    = false;
        $expected = "Cannot transliterate '$string': Unknown error";
        $actual   = NormalizerException::transliterationFailure($string, $error);
        self::assertSame($expected, $actual->getMessage());
    }

    public function testInvalidSuffix(): void
    {
        $suffix   = "$";
        $expected = "Invalid reserved word suffix '$': suffix can only contain '[a-zA-Z0-9_\\x80-\\xff]*' characters";
        $actual   = NormalizerException::invalidSuffix($suffix);
        self::assertSame($expected, $actual->getMessage());
    }
}
