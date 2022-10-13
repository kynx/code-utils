<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\PropertyNameNormalizer;
use Kynx\CodeUtils\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 * @uses \Kynx\CodeUtils\PhpLabel
 * @uses \Kynx\CodeUtils\WordCase
 *
 * @covers \Kynx\CodeUtils\PropertyNameNormalizer
 */
final class PropertyNameNormalizerTest extends TestCase
{
    /**
     * @dataProvider propertyNameProvider
     */
    public function testNormalizePropertyName(string $propertyName, WordCase $case, string $expected): void
    {
        $normalizer = new PropertyNameNormalizer($case);
        $actual     = $normalizer->normalize($propertyName);
        self::assertSame($expected, $actual);
    }

    public function propertyNameProvider(): array
    {
        return [
            'unicode_spellout' => ['€', WordCase::Camel, 'euro'],
            'ascii_spellout'   => ['$foo', WordCase::Camel, 'dollarFoo'],
            'reserved'         => ['class', WordCase::Camel, 'class'],
            'this'             => ['this', WordCase::Camel, 'this'],
            'snake_case'       => ['foo bar', WordCase::LowerSnake, 'foo_bar'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = 'fooBarBaz';
        $normalizer = new PropertyNameNormalizer(WordCase::Camel, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
