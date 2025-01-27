<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\AbstractNormalizer;
use Kynx\Code\Normalizer\AbstractUniqueLabeler;
use Kynx\Code\Normalizer\PropertyNameNormalizer;
use Kynx\Code\Normalizer\UniquePropertyLabeler;
use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UniquePropertyLabeler::class)]
#[UsesClass(AbstractNormalizer::class)]
#[UsesClass(AbstractUniqueLabeler::class)]
#[UsesClass(PropertyNameNormalizer::class)]
#[UsesClass(NumberSuffix::class)]
#[UsesClass(WordCase::class)]
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
