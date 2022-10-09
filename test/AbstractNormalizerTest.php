<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use AssertionError;
use Kynx\CodeUtils\AbstractNormalizer;
use Kynx\CodeUtils\NormalizerException;
use Kynx\CodeUtils\NormalizerInterface;
use PHPUnit\Framework\TestCase;

use function version_compare;

use const PHP_VERSION;

/**
 * @uses \Kynx\CodeUtils\NormalizerException
 *
 * @covers \Kynx\CodeUtils\AbstractNormalizer
 */
final class AbstractNormalizerTest extends TestCase
{
    /**
     * @dataProvider labelProvider
     */
    public function testNormalize(string $label, string $expected): void
    {
        $actual = $this->getNormalizer()->normalize($label);
        self::assertSame($expected, $actual);
    }

    public function labelProvider(): array
    {
        return [
            'pet_shop'         => ['ペット ショップ', 'PettoShoppu'],
            'combining_accent' => ['Маріу́поль', 'Mariupol'],
            'trailing_quote'   => ["Peoples'", 'PeoplesQuote'],
            'mixed_scripts'    => ['坏的 سيئ bad', 'HuaiDeSyyBad'],
            'symbol'           => ['€', 'Euro'],
            'low_ascii'        => ['Eat@Joes', 'EatAtJoes'],
            'high_ascii'       => ['£1 Shop', '£1Shop'],
            'trim'             => [' Foo ', 'Foo'],
            'whitespace'       => ["Foo \t\nBar", 'FooBar'],
            'separators'       => ['Foo.Bar-Baz', 'FooBarBaz'],
            'underscore'       => ['foo_bar', 'FooBar'],
            'lead_digits'      => ['12Foo', 'OneTwoFoo'],
            'single_digit'     => ['7', 'Seven'],
            'backtick'         => ["`cat /etc/passwd`", 'BacktickCatSlashEtcSlashPasswdBacktick'],
            'emoticon'         => ['I am 😭', 'IAmLoudlyCryingFace'],
            'weird'            => ["a ͖", 'ACombiningRightArrowheadAndUpArrowheadBelow'],
            'reserved'         => ['Class', 'ClassReserved'],
        ];
    }

    /**
     * @dataProvider caseProvider
     */
    public function testNormalizeUsesCase(string $case, string $expected): void
    {
        $actual = $this->getNormalizer($case)->normalize('foo bar');
        self::assertSame($expected, $actual);
    }

    public function caseProvider(): array
    {
        return [
            'camelCase'   => [NormalizerInterface::CAMEL_CASE, 'fooBar'],
            'PascalCase'  => [NormalizerInterface::PASCAL_CASE, 'FooBar'],
            'snake_case'  => [NormalizerInterface::SNAKE_CASE, 'foo_bar'],
            'UPPER_SNAKE' => [NormalizerInterface::UPPER_SNAKE, 'FOO_BAR'],
        ];
    }

    /**
     * @dataProvider spellOutCaseProvider
     */
    public function testSpellOutCase(string $case, string $label, string $expected): void
    {
        $actual = $this->getNormalizer($case)->normalize($label);
        self::assertSame($expected, $actual);
    }

    public function spellOutCaseProvider(): array
    {
        return [
            'nonascii-before-camelCase'   => [NormalizerInterface::CAMEL_CASE, '😫foo', 'tiredFaceFoo'],
            'nonascii-before-PascalCase'  => [NormalizerInterface::PASCAL_CASE, '😫foo', 'TiredFaceFoo'],
            'nonascii-before-snake_case'  => [NormalizerInterface::SNAKE_CASE, '😫foo', 'tired_face_foo'],
            'nonascii-before-UPPER_SNAKE' => [NormalizerInterface::UPPER_SNAKE, '😫foo', 'TIRED_FACE_FOO'],
            'nonascii-after-camelCase'    => [NormalizerInterface::CAMEL_CASE, 'foo😫', 'fooTiredFace'],
            'nonascii-after-PascalCase'   => [NormalizerInterface::PASCAL_CASE, 'foo😫', 'FooTiredFace'],
            'nonascii-after-snake_case'   => [NormalizerInterface::SNAKE_CASE, 'foo😫', 'foo_tired_face'],
            'nonascii-after-UPPER_SNAKE'  => [NormalizerInterface::UPPER_SNAKE, 'foo😫', 'FOO_TIRED_FACE'],
            'ascii-before-camelCase'      => [NormalizerInterface::CAMEL_CASE, '$foo', 'dollarFoo'],
            'ascii-before-PascalCase'     => [NormalizerInterface::PASCAL_CASE, '$foo', 'DollarFoo'],
            'ascii-before-snake_case'     => [NormalizerInterface::SNAKE_CASE, '$foo', 'dollar_foo'],
            'ascii-before-UPPER_SNAKE'    => [NormalizerInterface::UPPER_SNAKE, '$foo', 'DOLLAR_FOO'],
            'ascii-after-camelCase'       => [NormalizerInterface::CAMEL_CASE, 'foo$', 'fooDollar'],
            'ascii-after-PascalCase'      => [NormalizerInterface::PASCAL_CASE, 'foo$', 'FooDollar'],
            'ascii-after-snake_case'      => [NormalizerInterface::SNAKE_CASE, 'foo$', 'foo_dollar'],
            'ascii-after-UPPER_SNAKE'     => [NormalizerInterface::UPPER_SNAKE, 'foo$', 'FOO_DOLLAR'],
            'digit-before-camelCase'      => [NormalizerInterface::CAMEL_CASE, '1foo', 'oneFoo'],
            'digit-before-PascalCase'     => [NormalizerInterface::PASCAL_CASE, '1foo', 'OneFoo'],
            'digit-before-snake_case'     => [NormalizerInterface::SNAKE_CASE, '1foo', 'one_foo'],
            'digit-before-UPPER_SNAKE'    => [NormalizerInterface::UPPER_SNAKE, '1foo', 'ONE_FOO'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected = 'FooBarBaz';
        $actual   = $this->getNormalizer(NormalizerInterface::PASCAL_CASE, '|/')
            ->normalize('foo|bar /baz');
        self::assertSame($expected, $actual);
    }

    public function testConstructInvalidCaseThrowsException(): void
    {
        $case = 'tough';
        self::expectException(NormalizerException::class);
        self::expectExceptionMessage("Invalid case '$case'");
        $this->getNormalizer($case);
    }

    public function testPrepareSuffixAllowsNullAndAssertionErrorThrown(): void
    {
        if (
            version_compare(PHP_VERSION, '8.2.0-dev') >= 0
            && version_compare(PHP_VERSION, '8.2.0RC2') < 0
        ) {
            self::markTestSkipped('assert() not throwing exception in 8.2.0-RC1');
        }

        try {
            $normalizer = $this->getNormalizer(
                NormalizerInterface::CAMEL_CASE,
                NormalizerInterface::DEFAULT_SEPARATORS,
                null
            );
        } catch (NormalizerException) {
            self::fail("Null suffix threw exception");
        }

        self::expectException(AssertionError::class);
        self::expectExceptionMessage('assert($this->suffix !== null)');
        $normalizer->normalize('class');
    }

    /**
     * @dataProvider invalidSuffixProvider
     */
    public function testPrepareSuffixInvalidThrowsException(string $suffix): void
    {
        self::expectException(NormalizerException::class);
        self::expectExceptionMessage("Invalid reserved word suffix");
        $this->getNormalizer(
            NormalizerInterface::CAMEL_CASE,
            NormalizerInterface::DEFAULT_SEPARATORS,
            $suffix
        );
    }

    public function invalidSuffixProvider(): array
    {
        return [
            'empty'   => [''],
            'invalid' => ['$'],
        ];
    }

    public function testNormalizeThrowsTransliterationException(): void
    {
        $invalid  = "Iñtërnâtiôn\xE9àlizætiøn";
        $expected = "Cannot transliterate '$invalid': "
            . "String conversion of string to UTF-16 failed: U_INVALID_CHAR_FOUND";
        self::expectException(NormalizerException::class);
        self::expectExceptionMessage($expected);
        $this->getNormalizer()->normalize($invalid);
    }

    public function testNormalizeMultiple(): void
    {
        $labels   = [
            'มืดมนและมีพายุ',
            'اندھیرا اور طوفان',
            'काळोख आनी वादळी',
        ];
        $multiple = $this->getNormalizer();

        foreach ($labels as $label) {
            $expected = $this->getNormalizer()->normalize($label);
            $actual   = $multiple->normalize($label);
            self::assertSame($expected, $actual);
        }
    }

    public function testNormalizeTransliteratesAfterFailure(): void
    {
        $expected = "DingDong";
        $invalid  = "Iñtërnâtiôn\xE9àlizætiøn";

        $normalizer = $this->getNormalizer();
        try {
            $normalizer->normalize($invalid);
            self::fail("No exception thrown");
        } catch (NormalizerException) {
            // ignore
        }

        $actual = $normalizer->normalize('ding ₫');
        self::assertSame($expected, $actual);
    }

    /**
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    private function getNormalizer(
        string $case = NormalizerInterface::PASCAL_CASE,
        string $separators = NormalizerInterface::DEFAULT_SEPARATORS,
        string|null $suffix = 'Reserved'
    ): AbstractNormalizer {
        return new class ($suffix, $case, $separators) extends AbstractNormalizer {
            public function normalize(string $label): string
            {
                $ascii    = $this->toAscii($label);
                $spaced   = $this->separatorsToSpace($ascii);
                $speltOut = $this->spellOutAscii($spaced);
                $cased    = $this->toCase($speltOut);

                return $this->sanitizeReserved($cased);
            }
        };
    }
}
