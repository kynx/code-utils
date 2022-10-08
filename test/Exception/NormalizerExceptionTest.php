<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils\Exception;

use Kynx\CodeUtils\Exception\NormalizerException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\CodeUtils\Exception\NormalizerException
 */
final class NormalizerExceptionTest extends TestCase
{
    public function testTransliterationFailureWithErrorString(): void
    {
        $string = "abc";
        $error = "Foo";
        $expected = "Cannot transliterate '$string': $error";
        $actual = NormalizerException::transliterationFailure($string, $error);
        self::assertSame($expected, $actual->getMessage());
    }

    public function testTransliterationFailureWithFalseError(): void
    {
        $string = "abc";
        $error = false;
        $expected = "Cannot transliterate '$string': Unknown error";
        $actual = NormalizerException::transliterationFailure($string, $error);
        self::assertSame($expected, $actual->getMessage());
    }

    public function testInvalidCase(): void
    {
        $case = 'squiggly';
        $valid = ['straight', 'bent'];
        $expected = "Invalid case '$case': expected one of 'straight', 'bent'";
        $actual = NormalizerException::invalidCase($case, $valid);
        self::assertSame($expected, $actual->getMessage());
    }
}
