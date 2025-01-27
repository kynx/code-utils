<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\AbstractNormalizer;
use Kynx\Code\Normalizer\AbstractUniqueLabeler;
use Kynx\Code\Normalizer\PropertyNameNormalizer;
use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress InternalMethod
 */
#[CoversClass(AbstractUniqueLabeler::class)]
#[UsesClass(AbstractNormalizer::class)]
#[UsesClass(PropertyNameNormalizer::class)]
#[UsesClass(NumberSuffix::class)]
#[UsesClass(WordCase::class)]
final class AbstractUniqueLabelerTest extends TestCase
{
    /**
     * @param list<string> $labels
     */
    #[DataProvider('labelProvider')]
    public function testGetUnique(array $labels, bool $caseSensitive, array $expected): void
    {
        $namer  = new class (
            new PropertyNameNormalizer(),
            new NumberSuffix(),
            $caseSensitive
        ) extends AbstractUniqueLabeler {
        };
        $actual = $namer->getUnique($labels);
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, list{list<string>, bool, array<string, string>}>
     */
    public static function labelProvider(): array
    {
        return [
            'all_unique'       => [['a', 'b', 'c'], true, ['a' => 'a', 'b' => 'b', 'c' => 'c']],
            'not_unique'       => [['a b', 'a.b', 'a..b'], true, ['a b' => 'ab1', 'a.b' => 'ab2', 'a..b' => 'ab3']],
            'case_sensitive'   => [['a foo', 'a f oo'], true, ['a foo' => 'aFoo', 'a f oo' => 'afOo']],
            'case_insensitive' => [['a foo', 'a f oo'], false, ['a foo' => 'aFoo1', 'a f oo' => 'afOo2']],
        ];
    }
}
