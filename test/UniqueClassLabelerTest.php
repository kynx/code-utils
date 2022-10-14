<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\ClassNameNormalizer;
use Kynx\CodeUtils\UniqueClassLabeler;
use Kynx\CodeUtils\UniqueStrategy\NumberSuffix;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 * @uses \Kynx\CodeUtils\AbstractUniqueLabeler
 * @uses \Kynx\CodeUtils\ClassNameNormalizer
 * @uses \Kynx\CodeUtils\PhpLabel
 * @uses \Kynx\CodeUtils\UniqueStrategy\NumberSuffix
 * @uses \Kynx\CodeUtils\WordCase
 *
 * @covers \Kynx\CodeUtils\UniqueClassLabeler
 */
final class UniqueClassLabelerTest extends TestCase
{
    public function testConstructorSetsCaseInsensitive(): void
    {
        $expected = [
            'a foo'  => 'AFoo1',
            'a f oo' => 'AfOo2',
        ];
        $labeller = new UniqueClassLabeler(new ClassNameNormalizer('Foo'), new NumberSuffix());
        $actual   = $labeller->getUnique(['a foo', 'a f oo']);
        self::assertSame($expected, $actual);
    }
}
