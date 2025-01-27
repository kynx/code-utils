<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\AbstractNormalizer;
use Kynx\Code\Normalizer\PhpLabel;
use Kynx\Code\Normalizer\PropertyNameNormalizer;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PropertyNameNormalizer::class)]
#[UsesClass(AbstractNormalizer::class)]
#[UsesClass(PhpLabel::class)]
#[UsesClass(WordCase::class)]
final class PropertyNameNormalizerTest extends TestCase
{
    #[DataProvider('propertyNameProvider')]
    public function testNormalizePropertyName(string $propertyName, WordCase $case, string $expected): void
    {
        $normalizer = new PropertyNameNormalizer($case);
        $actual     = $normalizer->normalize($propertyName);
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, list{string, WordCase, string}>
     */
    public static function propertyNameProvider(): array
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
