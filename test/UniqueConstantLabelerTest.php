<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\ConstantNameNormalizer;
use Kynx\Code\Normalizer\UniqueConstantLabeler;
use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Code\Normalizer\AbstractNormalizer
 * @uses \Kynx\Code\Normalizer\AbstractUniqueLabeler
 * @uses \Kynx\Code\Normalizer\ConstantNameNormalizer
 * @uses \Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix
 * @uses \Kynx\Code\Normalizer\PhpLabel
 * @uses \Kynx\Code\Normalizer\WordCase
 *
 * @covers \Kynx\Code\Normalizer\UniqueConstantLabeler
 */
final class UniqueConstantLabelerTest extends TestCase
{
    public function testConstructorSetsCaseSensitive(): void
    {
        $expected = [
            'a foo'  => 'aFoo',
            'a f oo' => 'afOo',
        ];
        $labeller = new UniqueConstantLabeler(
            new ConstantNameNormalizer('Foo', WordCase::Camel),
            new NumberSuffix()
        );
        $actual   = $labeller->getUnique(['a foo', 'a f oo']);
        self::assertSame($expected, $actual);
    }
}
