<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\AbstractUniqueLabeler;
use Kynx\Code\Normalizer\PropertyNameNormalizer;
use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Code\Normalizer\AbstractNormalizer
 * @uses \Kynx\Code\Normalizer\PropertyNameNormalizer
 * @uses \Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix
 * @uses \Kynx\Code\Normalizer\WordCase
 *
 * @covers \Kynx\Code\Normalizer\AbstractUniqueLabeler
 * @psalm-suppress InternalMethod
 */
final class AbstractUniqueLabelerTest extends TestCase
{
    /**
     * @dataProvider labelProvider
     * @param list<string> $labels
     */
    public function testGetUnique(array $labels, bool $caseSensitive, array $expected): void
    {
        $namer  = self::getMockForAbstractClass(AbstractUniqueLabeler::class, [
            new PropertyNameNormalizer(),
            new NumberSuffix(),
            $caseSensitive,
        ]);
        $actual = $namer->getUnique($labels);
        self::assertSame($expected, $actual);
    }

    public function labelProvider(): array
    {
        return [
            'all_unique'       => [['a', 'b', 'c'], true, ['a' => 'a', 'b' => 'b', 'c' => 'c']],
            'not_unique'       => [['a b', 'a.b', 'a..b'], true, ['a b' => 'ab1', 'a.b' => 'ab2', 'a..b' => 'ab3']],
            'case_sensitive'   => [['a foo', 'a f oo'], true, ['a foo' => 'aFoo', 'a f oo' => 'afOo']],
            'case_insensitive' => [['a foo', 'a f oo'], false, ['a foo' => 'aFoo1', 'a f oo' => 'afOo2']],
        ];
    }
}
