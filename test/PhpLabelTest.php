<?php

declare(strict_types=1);

namespace KynxTest\Code\Normalizer;

use Kynx\Code\Normalizer\PhpLabel;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\Code\Normalizer\PhpLabel
 */
final class PhpLabelTest extends TestCase
{
    /**
     * @dataProvider isValidProvider
     */
    public function testIsValid(PhpLabel $label, string $string, bool $expected): void
    {
        $actual = $label->isValid($string);
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, list{PhpLabel, string, bool}>
     */
    public static function isValidProvider(): array
    {
        return [
            'label_empty'            => [PhpLabel::Label, '', false],
            'label_number_start'     => [PhpLabel::Label, '1abc', false],
            'label_hash_start'       => [PhpLabel::Label, '#abc', false],
            'label_dollar'           => [PhpLabel::Label, 'abc$def', false],
            'label_not_ascii'        => [PhpLabel::Label, 'abc€def', false],
            'label_high_ascii_start' => [PhpLabel::Label, '£abc', true],
            'label_letter'           => [PhpLabel::Label, 'a', true],
            'label_number'           => [PhpLabel::Label, 'a1', true],
            'prefix_empty'           => [PhpLabel::Prefix, '', false],
            'prefix_number'          => [PhpLabel::Prefix, '1', false],
            'prefix_hash'            => [PhpLabel::Prefix, '#', false],
            'prefix_not_ascii'       => [PhpLabel::Prefix, '€', false],
            'prefix_high_ascii'      => [PhpLabel::Prefix, '£', true],
            'suffix_empty'           => [PhpLabel::Suffix, '', false],
            'suffix_number'          => [PhpLabel::Suffix, '1', true],
            'suffix_hash'            => [PhpLabel::Suffix, '#', false],
            'suffix_not_ascii'       => [PhpLabel::Suffix, '€', false],
            'suffix_high_ascii'      => [PhpLabel::Suffix, '£', true],
            'variable_empty'         => [PhpLabel::Variable, '', false],
            'variable_no_dollar'     => [PhpLabel::Variable, 'abc', false],
            'variable_this'          => [PhpLabel::Variable, '$this', false],
            'variable_number_start'  => [PhpLabel::Variable, '$1', false],
            'variable_hash'          => [PhpLabel::Variable, '$a#', false],
            'variable_not_ascii'     => [PhpLabel::Variable, '$a€', false],
            'variable_high_ascii'    => [PhpLabel::Variable, '$£', true],
        ];
    }
}
