<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\UniqueStrategy\NumberSuffix;
use Kynx\CodeUtils\UniqueVariableLabeler;
use Kynx\CodeUtils\VariableNameNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 * @uses \Kynx\CodeUtils\AbstractUniqueLabeler
 * @uses \Kynx\CodeUtils\VariableNameNormalizer
 * @uses \Kynx\CodeUtils\UniqueStrategy\NumberSuffix
 * @uses \Kynx\CodeUtils\WordCase
 *
 * @covers \Kynx\CodeUtils\UniqueVariableLabeler
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
