<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\PropertyNameNormalizer;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Code\Normalizer\AbstractNormalizer
 * @uses \Kynx\Code\Normalizer\PhpLabel
 * @uses \Kynx\Code\Normalizer\WordCase
 *
 * @covers \Kynx\Code\Normalizer\PropertyNameNormalizer
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
