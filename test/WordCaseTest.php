<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\CodeUtils\WordCase
 */
final class WordCaseTest extends TestCase
{
    /**
     * @dataProvider stringProvider
     */
    public function testConvert(WordCase $case, string $string, string $expected): void
    {
        $actual = $case->convert($string);
        self::assertSame($expected, $actual);
    }

    public function stringProvider(): array
    {
        return [
            'empty'       => [WordCase::LowerSnake, '', ''],
            'just_spaces' => [WordCase::LowerSnake, "  ", ''],
            'trim'        => [WordCase::LowerSnake, " foo ", 'foo'],
            'numbers'     => [WordCase::LowerSnake, "foo123 bar", 'foo123_bar'],
            'underscore'  => [WordCase::LowerSnake, "one two_three", "one_two_three"],
            'camel'       => [WordCase::Camel, 'FOO bAR', 'fooBar'],
            'camelAbbr'   => [WordCase::Camel, 'a b foo', 'abFoo'],
            'lower_snake' => [WordCase::LowerSnake, 'FOO BAR', 'foo_bar'],
            'Pascal'      => [WordCase::Pascal, 'fOO bAR', 'FooBar'],
            'PascalAbbr'  => [WordCase::Pascal, 'a b foo', 'AbFoo'],
            'UPPER_SNAKE' => [WordCase::UpperSnake, 'foo bar', 'FOO_BAR'],
        ];
    }
}
