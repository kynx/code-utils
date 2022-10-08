<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\NormalizerInterface;
use Kynx\CodeUtils\PropertyNameNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 *
 * @covers \Kynx\CodeUtils\PropertyNameNormalizer
 */
final class PropertyNameNormalizerTest extends TestCase
{
    /**
     * @dataProvider propertyNameProvider
     */
    public function testNormalizePropertyName(string $propertyName, string $case, string $expected): void
    {
        $normalizer = new PropertyNameNormalizer($case);
        $actual     = $normalizer->normalize($propertyName);
        self::assertSame($expected, $actual);
    }

    public function propertyNameProvider(): array
    {
        return [
            'unicode_spellout' => ['€', NormalizerInterface::CAMEL_CASE, 'euro'],
            'ascii_spellout'   => ['$foo', NormalizerInterface::CAMEL_CASE, 'dollarFoo'],
            'reserved'         => ['class', NormalizerInterface::CAMEL_CASE, 'class'],
            'this'             => ['this', NormalizerInterface::CAMEL_CASE, 'this'],
            'snake_case'       => ['foo bar', NormalizerInterface::SNAKE_CASE, 'foo_bar'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = 'fooBarBaz';
        $normalizer = new PropertyNameNormalizer(NormalizerInterface::CAMEL_CASE, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
