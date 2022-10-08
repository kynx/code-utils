<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\NormalizerInterface;
use Kynx\CodeUtils\VariableNameNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
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
        string $case,
        string $expected
    ): void {
        $normalizer = new VariableNameNormalizer($thisReplacement, $case);
        $actual     = $normalizer->normalize($variableName);
        self::assertSame($expected, $actual);
    }

    public function variableNameProvider(): array
    {
        return [
            'unicode_spellout' => ['€', 'me', NormalizerInterface::CAMEL_CASE, '$euro'],
            'ascii_spellout'   => ['$foo', 'me', NormalizerInterface::CAMEL_CASE, '$dollarFoo'],
            'reserved'         => ['class', 'me', NormalizerInterface::CAMEL_CASE, '$class'],
            'this'             => ['this', 'me', NormalizerInterface::CAMEL_CASE, '$me'],
            '$replacement'     => ['this', '$me', NormalizerInterface::CAMEL_CASE, '$me'],
            'snake_case'       => ['foo bar', 'me', NormalizerInterface::SNAKE_CASE, '$foo_bar'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = '$fooBarBaz';
        $normalizer = new VariableNameNormalizer('Reserved', NormalizerInterface::CAMEL_CASE, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
