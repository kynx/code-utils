<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\AbstractNormalizer;
use Kynx\Code\Normalizer\PhpLabel;
use Kynx\Code\Normalizer\VariableNameNormalizer;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariableNameNormalizer::class)]
#[UsesClass(AbstractNormalizer::class)]
#[UsesClass(PhpLabel::class)]
#[UsesClass(WordCase::class)]
final class VariableNameNormalizerTest extends TestCase
{
    #[DataProvider('variableNameProvider')]
    public function testNormalizeVariableName(
        string $variableName,
        string $thisReplacement,
        WordCase $case,
        string $expected
    ): void {
        $normalizer = new VariableNameNormalizer($thisReplacement, $case);
        $actual     = $normalizer->normalize($variableName);
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, list{string, string, WordCase, string}>
     */
    public static function variableNameProvider(): array
    {
        return [
            'unicode_spellout' => ['€', 'me', WordCase::Camel, '$euro'],
            'ascii_spellout'   => ['$foo', 'me', WordCase::Camel, '$dollarFoo'],
            'reserved'         => ['class', 'me', WordCase::Camel, '$class'],
            'this'             => ['this', 'me', WordCase::Camel, '$me'],
            '$replacement'     => ['this', '$me', WordCase::Camel, '$me'],
            'snake_case'       => ['foo bar', 'me', WordCase::LowerSnake, '$foo_bar'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = '$fooBarBaz';
        $normalizer = new VariableNameNormalizer('Reserved', WordCase::Camel, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
