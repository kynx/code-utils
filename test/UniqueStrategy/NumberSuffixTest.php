<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer\UniqueStrategy;

use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Code\Normalizer\WordCase
 *
 * @covers \Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix
 */
final class NumberSuffixTest extends TestCase
{
    /**
     * @dataProvider labelProvider
     */
    public function testGenerateUniqueName(WordCase $case, string $label, int $occurrence, string $expected): void
    {
        $strategy = new NumberSuffix($case);
        $actual   = $strategy->generateUniqueName($label, $occurrence, 'bar');
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, list{WordCase, string, int, string}>
     */
    public static function labelProvider(): array
    {
        return [
            'first_snake'   => [WordCase::LowerSnake, 'foo', 1, 'foo_1'],
            'first_camel'   => [WordCase::Camel, 'foo', 1, 'foo1'],
            'twelfth_camel' => [WordCase::Camel, 'foo', 12, 'foo12'],
        ];
    }

    /**
     * @dataProvider suffixLabelProvider
     */
    public function testGenerateUniqueNameUsesSuffix(WordCase $case, string $label, string $expected): void
    {
        $strategy = new NumberSuffix($case, 'version');
        $actual   = $strategy->generateUniqueName($label, 2, 'bar');
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, list{WordCase, string, string}>
     */
    public static function suffixLabelProvider(): array
    {
        return [
            'camelCase'   => [WordCase::Camel, 'fooBar', 'fooBarVersion2'],
            'PascalCase'  => [WordCase::Pascal, 'FooBar', 'FooBarVersion2'],
            'snake_lower' => [WordCase::LowerSnake, 'foo', 'foo_version_2'],
            'SNAKE_UPPER' => [WordCase::UpperSnake, 'FOO', 'FOO_VERSION_2'],
        ];
    }
}
