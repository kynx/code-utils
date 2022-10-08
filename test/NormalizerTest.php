<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\Exception\NormalizerException;
use Kynx\CodeUtils\Normalizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\CodeUtils\Normalizer
 * @uses \Kynx\CodeUtils\Exception\NormalizerException
 */
final class NormalizerTest extends TestCase
{
    public function testNormalizeUsesSeparators(): void
    {
        $expected   = 'FooBarBaz';
        $normalizer = new Normalizer('|/');
        $actual     = $normalizer->normalizeClassName('Foo|Bar/ Baz', 'Reserved');
        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider classNameProvider
     */
    public function testNormalizeClassName(string $name, string $case, string $expected): void
    {
        $normalizer = new Normalizer();
        $actual     = $normalizer->normalizeClassName($name, 'Reserved', $case);
        self::assertSame($expected, $actual);
    }

    public function classNameProvider(): array
    {
        return [
            'comb_accent'  => ['Маріу́поль', Normalizer::PASCAL_CASE, 'Mariupol'],
            'symbol'       => ['€', Normalizer::PASCAL_CASE, 'Euro'],
            'low_ascii'    => ['Eat@Joes', Normalizer::PASCAL_CASE, 'EatAtJoes'],
            'high_ascii'   => ['£1 Shop', Normalizer::PASCAL_CASE, '£1Shop'],
            'trim'         => [' FooBar ', Normalizer::PASCAL_CASE, 'FooBar'],
            'whitespace'   => ["Foo \t\nBar", Normalizer::PASCAL_CASE, 'FooBar'],
            'separators'   => ['Foo.Bar-Baz', Normalizer::PASCAL_CASE, 'FooBarBaz'],
            'lead_digits'  => ['12Foo', Normalizer::PASCAL_CASE, 'OneTwoFoo'],
            'single_digit' => ['7', Normalizer::PASCAL_CASE, 'Seven'],
            'backtick'     => ["`cat /etc/passwd`", Normalizer::PASCAL_CASE, 'BacktickCatSlashEtcSlashPasswdBacktick'],
            'emoticon'     => ['I am 😭', Normalizer::PASCAL_CASE, 'IAmLoudlyCryingFace'],
            'weird'        => ["a ͖", Normalizer::PASCAL_CASE, 'ACombiningRightArrowheadAndUpArrowheadBelow'],
            'reserved'     => ['Class', Normalizer::PASCAL_CASE, 'ClassReserved'],
            'camelCase'    => ['foo bar', Normalizer::CAMEL_CASE, 'fooBar'],
            'snake_case'   => ['foo bar', Normalizer::SNAKE_CASE, 'foo_bar'],
        ];
    }

    /**
     * @dataProvider constantNameProvider
     */
    public function testNormalizeConstantName(string $constantName, string $case, string $expected): void
    {
        $normalizer = new Normalizer();
        $actual     = $normalizer->normalizeConstantName($constantName, 'RESERVED', $case);
        self::assertSame($expected, $actual);
    }

    public function constantNameProvider(): array
    {
        return [
            'symbol'      => ['€ sign', Normalizer::UPPER_SNAKE, 'EURO_SIGN'],
            'lead_digits' => ['12 foo', Normalizer::UPPER_SNAKE, 'ONE_TWO_FOO'],
            'reserved'    => ['exit', Normalizer::UPPER_SNAKE, 'EXIT_RESERVED'],
            'PascalCase'  => ['foo bar', Normalizer::PASCAL_CASE, 'FooBar'],
            'camelCase'   => ['foo bar', Normalizer::CAMEL_CASE, 'fooBar'],
            'snake_case'  => ['foo bar', Normalizer::SNAKE_CASE, 'foo_bar'],
        ];
    }

    /**
     * @dataProvider namespaceProvider
     */
    public function testNormalizeNamespace(string $namespace, string $case, string $expected): void
    {
        $normalizer = new Normalizer();
        $actual     = $normalizer->normalizeNamespace($namespace, 'Reserved', $case);
        self::assertSame($expected, $actual);
    }

    public function namespaceProvider(): array
    {
        return [
            'pet_shop'           => ['ペット \ ショップ', Normalizer::PASCAL_CASE, 'Petto\Shoppu'],
            'leading_backslash'  => ['\foo\bar', Normalizer::PASCAL_CASE, 'Foo\Bar'],
            'trailing_backslash' => ['foo\bar\\', Normalizer::PASCAL_CASE, 'Foo\Bar'],
            'empty_namespace'    => ['foo\\ \\bar', Normalizer::PASCAL_CASE, 'Foo\Bar'],
            'reserved'           => ['global\fashion', Normalizer::PASCAL_CASE, 'GlobalReserved\Fashion'],
            'leading_digits'     => ['cat\9lives', Normalizer::PASCAL_CASE, 'Cat\NineLives'],
            'camelCase'          => ['home \ sweet home', Normalizer::CAMEL_CASE, 'home\sweetHome'],
            'snake_case'         => ['home \ sweet home', Normalizer::SNAKE_CASE, 'home\sweet_home'],
        ];
    }

    /**
     * @dataProvider propertyNameProvider
     */
    public function testNormalizePropertyName(string $propertyName, string $case, string $expected): void
    {
        $normalizer = new Normalizer();
        $actual     = $normalizer->normalizePropertyName($propertyName, $case);
        self::assertSame($expected, $actual);
    }

    public function propertyNameProvider(): array
    {
        return [
            'dollar'     => ['$foo', Normalizer::CAMEL_CASE, 'dollarFoo'],
            'reserved'   => ['class', Normalizer::CAMEL_CASE, 'class'],
            'this'       => ['this', Normalizer::CAMEL_CASE, 'this'],
            'PascalCase' => ['foo bar', Normalizer::PASCAL_CASE, 'FooBar'],
            'snake_case' => ['foo bar', Normalizer::SNAKE_CASE, 'foo_bar'],
        ];
    }

    /**
     * @dataProvider variableNameProvider
     */
    public function testNormalizeVariableName(string $variableName, string $case, string $expected): void
    {
        $normalizer = new Normalizer();
        $actual     = $normalizer->normalizeVariableName($variableName, 'Reserved', $case);
        self::assertSame($expected, $actual);
    }

    public function variableNameProvider(): array
    {
        return [
            'dollar'     => ['$foo', Normalizer::CAMEL_CASE, '$dollarFoo'],
            'reserved'   => ['class', Normalizer::CAMEL_CASE, '$class'],
            'this'       => ['this', Normalizer::CAMEL_CASE, '$thisReserved'],
            'PascalCase' => ['foo bar', Normalizer::PASCAL_CASE, '$FooBar'],
            'snake_case' => ['foo bar', Normalizer::SNAKE_CASE, '$foo_bar'],
        ];
    }

    public function testNormalizeThrowsTransliterationException(): void
    {
        $invalid    = "Iñtërnâtiôn\xE9àlizætiøn";
        $expected   = "Cannot transliterate '$invalid': "
            . "String conversion of string to UTF-16 failed: U_INVALID_CHAR_FOUND";
        $normalizer = new Normalizer();
        self::expectException(NormalizerException::class);
        self::expectExceptionMessage($expected);
        $normalizer->normalizeClassName($invalid, 'Reserved');
    }

    public function testNormalizeInvalidCaseThrowsException(): void
    {
        $case = 'tough';
        $normalizer = new Normalizer();
        self::expectException(NormalizerException::class);
        self::expectExceptionMessage("Invalid case '$case'");
        $normalizer->normalizeClassName("foo", "Reserved", $case);
    }

    public function testNormalizeTransliteratesAfterFailure(): void
    {
        $expected   = "DingDong";
        $invalid    = "Iñtërnâtiôn\xE9àlizætiøn";
        $normalizer = new Normalizer();

        try {
            $normalizer->normalizeClassName($invalid, 'Reserved');
            self::fail("No exception thrown");
        } catch (NormalizerException) {
            // ignore
        }

        $actual = $normalizer->normalizeClassName('ding ₫', 'Reserved');
        self::assertSame($expected, $actual);
    }
}
