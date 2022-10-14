<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\ConstantNameNormalizer;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\Code\Normalizer\AbstractNormalizer
 * @uses \Kynx\Code\Normalizer\PhpLabel
 * @uses \Kynx\Code\Normalizer\WordCase
 *
 * @covers \Kynx\Code\Normalizer\ConstantNameNormalizer
 */
final class ConstantNameNormalizerTest extends TestCase
{
    /**
     * @dataProvider constantNameProvider
     */
    public function testNormalize(string $constantName, WordCase $case, string $expected): void
    {
        $normalizer = new ConstantNameNormalizer('RESERVED', $case);
        $actual     = $normalizer->normalize($constantName);
        self::assertSame($expected, $actual);
    }

    public function constantNameProvider(): array
    {
        return [
            'unicode_spellout' => ['€ sign', WordCase::UpperSnake, 'EURO_SIGN'],
            'ascii_spellout'   => ['$', WordCase::UpperSnake, 'DOLLAR'],
            'reserved'         => ['exit', WordCase::UpperSnake, 'EXIT_RESERVED'],
            'lead_digits'      => ['12 foo', WordCase::UpperSnake, 'ONE_TWO_FOO'],
            'PascalCase'       => ['foo bar', WordCase::Pascal, 'FooBar'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = 'FOO_BAR_BAZ';
        $normalizer = new ConstantNameNormalizer('Foo', WordCase::UpperSnake, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
