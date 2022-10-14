<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils\UniqueStrategy;

use Kynx\CodeUtils\UniqueStrategy\SpellOutOrdinalPrefix;
use Kynx\CodeUtils\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\WordCase
 *
 * @covers \Kynx\CodeUtils\UniqueStrategy\SpellOutOrdinalPrefix
 */
final class SpellOutOrdinalPrefixTest extends TestCase
{
    /**
     * @dataProvider labelProvider
     */
    public function testGenerateUniqueName(WordCase $case, string $label, int $occurrence, string $expected): void
    {
        $strategy = new SpellOutOrdinalPrefix($case);
        $actual   = $strategy->generateUniqueName($label, $occurrence, 'blah');
        self::assertSame($expected, $actual);
    }

    public function labelProvider(): array
    {
        return [
            'first_snake'         => [WordCase::LowerSnake, 'snake', 1, 'first_snake'],
            'first_camel'         => [WordCase::Camel, 'camel', 1, 'firstCamel'],
            'one_hundredth_camel' => [WordCase::Pascal, 'Pascal', 100, 'OneHundredthPascal'],
        ];
    }
}
