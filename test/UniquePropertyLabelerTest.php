<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\PropertyNameNormalizer;
use Kynx\Code\Normalizer\UniquePropertyLabeler;
use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Code\Normalizer\AbstractNormalizer
 * @uses \Kynx\Code\Normalizer\AbstractUniqueLabeler
 * @uses \Kynx\Code\Normalizer\PropertyNameNormalizer
 * @uses \Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix
 * @uses \Kynx\Code\Normalizer\WordCase
 *
 * @covers \Kynx\Code\Normalizer\UniquePropertyLabeler
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
