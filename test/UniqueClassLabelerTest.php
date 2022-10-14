<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\ClassNameNormalizer;
use Kynx\Code\Normalizer\UniqueClassLabeler;
use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Code\Normalizer\AbstractNormalizer
 * @uses \Kynx\Code\Normalizer\AbstractUniqueLabeler
 * @uses \Kynx\Code\Normalizer\ClassNameNormalizer
 * @uses \Kynx\Code\Normalizer\PhpLabel
 * @uses \Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix
 * @uses \Kynx\Code\Normalizer\WordCase
 *
 * @covers \Kynx\Code\Normalizer\UniqueClassLabeler
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
