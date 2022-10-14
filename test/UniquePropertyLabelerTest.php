<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\PropertyNameNormalizer;
use Kynx\CodeUtils\UniquePropertyLabeler;
use Kynx\CodeUtils\UniqueStrategy\NumberSuffix;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 * @uses \Kynx\CodeUtils\AbstractUniqueLabeler
 * @uses \Kynx\CodeUtils\PropertyNameNormalizer
 * @uses \Kynx\CodeUtils\UniqueStrategy\NumberSuffix
 * @uses \Kynx\CodeUtils\WordCase
 *
 * @covers \Kynx\CodeUtils\UniquePropertyLabeler
 */
final class UniquePropertyLabelerTest extends TestCase
{
    public function testConstructorSetsCaseSensitive(): void
    {
        $expected = [
            'a foo'  => 'aFoo',
            'a f oo' => 'afOo',
        ];
        $labeller = new UniquePropertyLabeler(new PropertyNameNormalizer(), new NumberSuffix());
        $actual   = $labeller->getUnique(['a foo', 'a f oo']);
        self::assertSame($expected, $actual);
    }
}
