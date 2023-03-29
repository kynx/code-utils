<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\ClassConstantNameNormalizer;
use Kynx\Code\Normalizer\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Code\Normalizer\ClassConstantNameNormalizer
 */
final class ClassConstantNameNormalizerTest extends TestCase
{
    /**
     * @dataProvider constantNameProvider
     */
    public function testNormalize(string $constantName, WordCase $case, string $expected): void
    {
        $normalizer = new ClassConstantNameNormalizer('RESERVED', $case);
        $actual     = $normalizer->normalize($constantName);
        self::assertSame($expected, $actual);
    }

    public function constantNameProvider(): array
    {
        return [
            'unicode_spellout' => ['€ sign', WordCase::UpperSnake, 'EURO_SIGN'],
            'ascii_spellout'   => ['$', WordCase::UpperSnake, 'DOLLAR'],
            'const_reserved'   => ['exit', WordCase::UpperSnake, 'EXIT'],
            'class'            => ['class', WordCase::UpperSnake, 'CLASS_RESERVED'],
            'lead_digits'      => ['12 foo', WordCase::UpperSnake, 'ONE_TWO_FOO'],
            'PascalCase'       => ['foo bar', WordCase::Pascal, 'FooBar'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = 'FOO_BAR_BAZ';
        $normalizer = new ClassConstantNameNormalizer('Foo', WordCase::UpperSnake, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
