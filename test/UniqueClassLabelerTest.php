<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\AbstractNormalizer;
use Kynx\Code\Normalizer\AbstractUniqueLabeler;
use Kynx\Code\Normalizer\ClassNameNormalizer;
use Kynx\Code\Normalizer\PhpLabel;
use Kynx\Code\Normalizer\UniqueClassLabeler;
use Kynx\Code\Normalizer\UniqueStrategy\NumberSuffix;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UniqueClassLabeler::class)]
#[UsesClass(AbstractNormalizer::class)]
#[UsesClass(AbstractUniqueLabeler::class)]
#[UsesClass(ClassNameNormalizer::class)]
#[UsesClass(PhpLabel::class)]
#[UsesClass(NumberSuffix::class)]
#[UsesClass(WordCase::class)]
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
