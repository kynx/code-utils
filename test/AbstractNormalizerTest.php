<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\AbstractNormalizer;
use Kynx\CodeUtils\NormalizerException;
use Kynx\CodeUtils\NormalizerInterface;
use PHPUnit\Framework\TestCase;

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
            'mixed_scripts'    => ['坏的 سيئ bad', 'HuaiDeSyyBad'],
            'symbol'           => ['€', 'Euro'],
            'low_ascii'        => ['Eat@Joes', 'EatAtJoes'],
            'high_ascii'       => ['£1 Shop', '£1Shop'],
            'trim'             => [' FooBar ', 'FooBar'],
            'whitespace'       => ["Foo \t\nBar", 'FooBar'],
            'separators'       => ['Foo.Bar-Baz', 'FooBarBaz'],
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
        self::getMockForAbstractClass(AbstractNormalizer::class, ['a', $case]);
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
        string $separators = NormalizerInterface::DEFAULT_SEPARATORS
    ): AbstractNormalizer {
        return new class ('Reserved', $case, $separators) extends AbstractNormalizer {
            public function normalize(string $label): string
            {
                $ascii       = $this->toAscii($label);
                $underscored = $this->separatorsToUnderscore($ascii);
                $speltOut    = $this->spellOutAscii($underscored);
                $cased       = $this->toCase($speltOut);

                return $this->sanitizeReserved($cased, self::RESERVED);
            }
        };
    }
}
