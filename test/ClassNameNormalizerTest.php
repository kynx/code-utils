<?php

declare(strict_types=1);

namespace KynxTest\CodeUtils;

use Kynx\CodeUtils\ClassNameNormalizer;
use Kynx\CodeUtils\NormalizerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\CodeUtils\AbstractNormalizer
 *
 * @covers \Kynx\CodeUtils\ClassNameNormalizer
 */
final class ClassNameNormalizerTest extends TestCase
{
    /**
     * @dataProvider classNameProvider
     */
    public function testNormalize(string $className, string $case, string $expected): void
    {
        $normalizer = new ClassNameNormalizer('Reserved', $case);
        $actual     = $normalizer->normalize($className);
        self::assertSame($expected, $actual);
    }

    public function classNameProvider(): array
    {
        return [
            'unicode_spellout'   => ['€', NormalizerInterface::PASCAL_CASE, 'Euro'],
            'ascii_spellout'     => ['$', NormalizerInterface::PASCAL_CASE, 'Dollar'],
            'reserved'           => ['global\fashion', NormalizerInterface::PASCAL_CASE, 'GlobalReserved\Fashion'],
            'leading_backslash'  => ['\foo\bar', NormalizerInterface::PASCAL_CASE, 'Foo\Bar'],
            'trailing_backslash' => ['foo\bar\\', NormalizerInterface::PASCAL_CASE, 'Foo\Bar'],
            'empty_namespace'    => ['foo\\ \\bar', NormalizerInterface::PASCAL_CASE, 'Foo\Bar'],
            'leading_digits'     => ['cat\9lives', NormalizerInterface::PASCAL_CASE, 'Cat\NineLives'],
            'camelCase'          => ['home \ sweet home', NormalizerInterface::CAMEL_CASE, 'home\sweetHome'],
        ];
    }

    public function testNormalizeUsesSeparators(): void
    {
        $expected   = 'FooBarBaz';
        $normalizer = new ClassNameNormalizer('Reserved', NormalizerInterface::PASCAL_CASE, '|/');
        $actual     = $normalizer->normalize('Foo|Bar/ Baz');
        self::assertSame($expected, $actual);
    }
}
