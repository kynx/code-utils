<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\VariableNameNormalizer;
use Kynx\CodeUtils\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 * @uses \Kynx\CodeUtils\PhpLabel
 * @uses \Kynx\CodeUtils\WordCase
 *
 * @covers \Kynx\CodeUtils\VariableNameNormalizer
 */
final class VariableNameNormalizerTest extends TestCase
{
    /**
     * @dataProvider variableNameProvider
     */
    public function testNormalizeVariableName(
        string $variableName,
        string $thisReplacement,
        WordCase $case,
        string $expected
    ): void {
        $normalizer = new VariableNameNormalizer($thisReplacement, $case);
        $actual     = $normalizer->normalize($variableName);
        self::assertSame($expected, $actual);
    }

    public function variableNameProvider(): array
    {
        return [
            'unicode_spellout' => ['€', 'me', WordCase::Camel, '$euro'],
            'ascii_spellout'   => ['$foo', 'me', WordCase::Camel, '$dollarFoo'],
            'reserved'         => ['class', 'me', WordCase::Camel, '$class'],
            'this'             => ['this', 'me', WordCase::Camel, '$me'],
            '$replacement'     => ['this', '$me', WordCase::Camel, '$me'],
            'snake_case'       => ['foo bar', 'me', WordCase::LowerSnake, '$foo_bar'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = '$fooBarBaz';
        $normalizer = new VariableNameNormalizer('Reserved', WordCase::Camel, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
