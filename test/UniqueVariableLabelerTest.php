<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use Kynx\Code\Normalizer\UniqueVariableLabeler;
use Kynx\Code\Normalizer\VariableNameNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Code\Normalizer\AbstractNormalizer
 * @uses \Kynx\Code\Normalizer\AbstractUniqueLabeler
 * @uses \Kynx\Code\Normalizer\VariableNameNormalizer
 * @uses \Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix
 * @uses \Kynx\Code\Normalizer\WordCase
 *
 * @covers \Kynx\Code\Normalizer\UniqueVariableLabeler
 */
final class UniqueVariableLabelerTest extends TestCase
{
    public function testConstructorSetsCaseSensitive(): void
    {
        $expected = [
            'a foo'  => '$aFoo',
            'a f oo' => '$afOo',
        ];
        $labeller = new UniqueVariableLabeler(new VariableNameNormalizer(), new NumberSuffix());
        $actual   = $labeller->getUnique(['a foo', 'a f oo']);
        self::assertSame($expected, $actual);
    }
}
