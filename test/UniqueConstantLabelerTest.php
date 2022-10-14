<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\ConstantNameNormalizer;
use Kynx\CodeUtils\UniqueConstantLabeler;
use Kynx\CodeUtils\UniqueStrategy\NumberSuffix;
use Kynx\CodeUtils\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 * @uses \Kynx\CodeUtils\AbstractUniqueLabeler
 * @uses \Kynx\CodeUtils\ConstantNameNormalizer
 * @uses \Kynx\CodeUtils\UniqueStrategy\NumberSuffix
 * @uses \Kynx\CodeUtils\PhpLabel
 * @uses \Kynx\CodeUtils\WordCase
 *
 * @covers \Kynx\CodeUtils\UniqueConstantLabeler
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
