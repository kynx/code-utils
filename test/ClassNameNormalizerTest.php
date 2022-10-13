<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\ClassNameNormalizer;
use Kynx\CodeUtils\WordCase;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 * @uses \Kynx\CodeUtils\PhpLabel
 * @uses \Kynx\CodeUtils\WordCase
 *
 * @covers \Kynx\CodeUtils\ClassNameNormalizer
 */
final class ClassNameNormalizerTest extends TestCase
{
    /**
     * @dataProvider classNameProvider
     */
    public function testNormalize(string $className, WordCase $case, string $expected): void
    {
        $normalizer = new ClassNameNormalizer('Reserved', $case);
        $actual     = $normalizer->normalize($className);
        self::assertSame($expected, $actual);
    }

    public function classNameProvider(): array
    {
        return [
            'unicode_spellout'   => ['€', WordCase::Pascal, 'Euro'],
            'ascii_spellout'     => ['$', WordCase::Pascal, 'Dollar'],
            'reserved'           => ['global\fashion', WordCase::Pascal, 'GlobalReserved\Fashion'],
            'leading_backslash'  => ['\foo\bar', WordCase::Pascal, 'Foo\Bar'],
            'trailing_backslash' => ['foo\bar\\', WordCase::Pascal, 'Foo\Bar'],
            'empty_namespace'    => ['foo\\ \\bar', WordCase::Pascal, 'Foo\Bar'],
            'leading_digits'     => ['cat\9lives', WordCase::Pascal, 'Cat\NineLives'],
            'camelCase'          => ['home \ sweet home', WordCase::Camel, 'home\sweetHome'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = 'FooBarBaz';
        $normalizer = new ClassNameNormalizer('Reserved', WordCase::Pascal, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
