<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\AbstractNormalizer;
use Kynx\Code\Normalizer\AbstractUniqueLabeler;
use Kynx\Code\Normalizer\ConstantNameNormalizer;
use Kynx\Code\Normalizer\PhpLabel;
use Kynx\Code\Normalizer\UniqueConstantLabeler;
use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UniqueConstantLabeler::class)]
#[UsesClass(AbstractNormalizer::class)]
#[UsesClass(AbstractUniqueLabeler::class)]
#[UsesClass(ConstantNameNormalizer::class)]
#[UsesClass(NumberSuffix::class)]
#[UsesClass(PhpLabel::class)]
#[UsesClass(WordCase::class)]
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
