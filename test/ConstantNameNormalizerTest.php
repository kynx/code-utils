<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\ConstantNameNormalizer;
use Kynx\CodeUtils\NormalizerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 *
 * @covers \Kynx\CodeUtils\ConstantNameNormalizer
 */
final class ConstantNameNormalizerTest extends TestCase
{
    /**
     * @dataProvider constantNameProvider
     */
    public function testNormalize(string $constantName, string $case, string $expected): void
    {
        $normalizer = new ConstantNameNormalizer('RESERVED', $case);
        $actual     = $normalizer->normalize($constantName);
        self::assertSame($expected, $actual);
    }

    public function constantNameProvider(): array
    {
        return [
            'unicode_spellout' => ['€ sign', NormalizerInterface::UPPER_SNAKE, 'EURO_SIGN'],
            'ascii_spellout'   => ['$', NormalizerInterface::UPPER_SNAKE, 'DOLLAR'],
            'reserved'         => ['exit', NormalizerInterface::UPPER_SNAKE, 'EXIT_RESERVED'],
            'lead_digits'      => ['12 foo', NormalizerInterface::UPPER_SNAKE, 'ONE_TWO_FOO'],
            'PascalCase'       => ['foo bar', NormalizerInterface::PASCAL_CASE, 'FooBar'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = 'FOO_BAR_BAZ';
        $normalizer = new ConstantNameNormalizer('Foo', NormalizerInterface::UPPER_SNAKE, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
