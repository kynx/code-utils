<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(WordCase::class)]
final class WordCaseTest extends TestCase
{
    #[DataProvider('stringProvider')]
    public function testConvert(WordCase $case, string $string, string $expected): void
    {
        $actual = $case->convert($string);
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, list{WordCase, string, string}>
     */
    public static function stringProvider(): array
    {
        return [
            'empty'          => [WordCase::LowerSnake, '', ''],
            'just_spaces'    => [WordCase::LowerSnake, "  ", ''],
            'trim'           => [WordCase::LowerSnake, " foo ", 'foo'],
            'numbers'        => [WordCase::LowerSnake, "foo123 bar", 'foo123_bar'],
            'underscore'     => [WordCase::LowerSnake, "one two_three", "one_two_three"],
            'camel'          => [WordCase::Camel, 'Foo bar', 'fooBar'],
            'camelAbbr'      => [WordCase::Camel, 'a b foo', 'abFoo'],
            'camelPreserve'  => [WordCase::Camel, 'FooBar', 'fooBar'],
            'lower_snake'    => [WordCase::LowerSnake, 'FOO BAR', 'foo_bar'],
            'Pascal'         => [WordCase::Pascal, 'foo bar', 'FooBar'],
            'PascalAbbr'     => [WordCase::Pascal, 'a b foo', 'AbFoo'],
            'PascalPreserve' => [WordCase::Pascal, 'fooBar', 'FooBar'],
            'UPPER_SNAKE'    => [WordCase::UpperSnake, 'foo bar', 'FOO_BAR'],
        ];
    }
}
