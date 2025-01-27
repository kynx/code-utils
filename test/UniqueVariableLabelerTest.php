<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\AbstractNormalizer;
use Kynx\Code\Normalizer\AbstractUniqueLabeler;
use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use Kynx\Code\Normalizer\UniqueVariableLabeler;
use Kynx\Code\Normalizer\VariableNameNormalizer;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UniqueVariableLabeler::class)]
#[UsesClass(AbstractNormalizer::class)]
#[UsesClass(AbstractUniqueLabeler::class)]
#[UsesClass(VariableNameNormalizer::class)]
#[UsesClass(NumberSuffix::class)]
#[UsesClass(WordCase::class)]
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
