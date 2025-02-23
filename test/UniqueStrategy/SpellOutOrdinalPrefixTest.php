<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer\UniqueStrategy;

use Kynx\Code\Normalizer\UniqueStrategy\SpellOutOrdinalPrefix;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SpellOutOrdinalPrefix::class)]
#[UsesClass(WordCase::class)]
final class SpellOutOrdinalPrefixTest extends TestCase
{
    #[DataProvider('labelProvider')]
    public function testGenerateUniqueName(WordCase $case, string $label, int $occurrence, string $expected): void
    {
        $strategy = new SpellOutOrdinalPrefix($case);
        $actual   = $strategy->generateUniqueName($label, $occurrence, 'blah');
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, list{WordCase, string, int, string}>
     */
    public static function labelProvider(): array
    {
        return [
            'first_snake'         => [WordCase::LowerSnake, 'snake', 1, 'first_snake'],
            'first_camel'         => [WordCase::Camel, 'camel', 1, 'firstCamel'],
            'one_hundredth_camel' => [WordCase::Pascal, 'Pascal', 100, 'OneHundredthPascal'],
        ];
    }
}
