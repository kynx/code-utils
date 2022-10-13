<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use AssertionError;
use Kynx\CodeUtils\AbstractNormalizer;
use Kynx\CodeUtils\NormalizerException;
use Kynx\CodeUtils\NormalizerInterface;
use Kynx\CodeUtils\WordCase;
use PHPUnit\Framework\TestCase;

use function version_compare;

use const PHP_VERSION;

/**
 * @uses \Kynx\CodeUtils\NormalizerException
 * @uses \Kynx\CodeUtils\PhpLabel
 * @uses \Kynx\CodeUtils\WordCase
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
    public function testNormalizeUsesCase(WordCase $case, string $expected): void
    {
        $actual = $this->getNormalizer($case)->normalize('foo bar');
        self::assertSame($expected, $actual);
    }

    public function caseProvider(): array
    {
        return [
            'camelCase'   => [WordCase::Camel, 'fooBar'],
            'PascalCase'  => [WordCase::Pascal, 'FooBar'],
            'snake_case'  => [WordCase::LowerSnake, 'foo_bar'],
            'UPPER_SNAKE' => [WordCase::UpperSnake, 'FOO_BAR'],
        ];
    }

    /**
     * @dataProvider spellOutCaseProvider
     */
    public function testSpellOutCase(WordCase $case, string $label, string $expected): void
    {
        $actual = $this->getNormalizer($case)->normalize($label);
        self::assertSame($expected, $actual);
    }

    public function spellOutCaseProvider(): array
    {
        return [
            'nonascii-before-camelCase'   => [WordCase::Camel, '😫foo', 'tiredFaceFoo'],
            'nonascii-before-PascalCase'  => [WordCase::Pascal, '😫foo', 'TiredFaceFoo'],
            'nonascii-before-snake_case'  => [WordCase::LowerSnake, '😫foo', 'tired_face_foo'],
            'nonascii-before-UPPER_SNAKE' => [WordCase::UpperSnake, '😫foo', 'TIRED_FACE_FOO'],
            'nonascii-after-camelCase'    => [WordCase::Camel, 'foo😫', 'fooTiredFace'],
            'nonascii-after-PascalCase'   => [WordCase::Pascal, 'foo😫', 'FooTiredFace'],
            'nonascii-after-snake_case'   => [WordCase::LowerSnake, 'foo😫', 'foo_tired_face'],
            'nonascii-after-UPPER_SNAKE'  => [WordCase::UpperSnake, 'foo😫', 'FOO_TIRED_FACE'],
            'ascii-before-camelCase'      => [WordCase::Camel, '$foo', 'dollarFoo'],
            'ascii-before-PascalCase'     => [WordCase::Pascal, '$foo', 'DollarFoo'],
            'ascii-before-snake_case'     => [WordCase::LowerSnake, '$foo', 'dollar_foo'],
            'ascii-before-UPPER_SNAKE'    => [WordCase::UpperSnake, '$foo', 'DOLLAR_FOO'],
            'ascii-after-camelCase'       => [WordCase::Camel, 'foo$', 'fooDollar'],
            'ascii-after-PascalCase'      => [WordCase::Pascal, 'foo$', 'FooDollar'],
            'ascii-after-snake_case'      => [WordCase::LowerSnake, 'foo$', 'foo_dollar'],
            'ascii-after-UPPER_SNAKE'     => [WordCase::UpperSnake, 'foo$', 'FOO_DOLLAR'],
            'digit-before-camelCase'      => [WordCase::Camel, '1foo', 'oneFoo'],
            'digit-before-PascalCase'     => [WordCase::Pascal, '1foo', 'OneFoo'],
            'digit-before-snake_case'     => [WordCase::LowerSnake, '1foo', 'one_foo'],
            'digit-before-UPPER_SNAKE'    => [WordCase::UpperSnake, '1foo', 'ONE_FOO'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected = 'FooBarBaz';
        $actual   = $this->getNormalizer(WordCase::Pascal, '|/')
            ->normalize('foo|bar /baz');
        self::assertSame($expected, $actual);
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
                WordCase::Camel,
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
            WordCase::Camel,
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
        WordCase $case = WordCase::Pascal,
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
