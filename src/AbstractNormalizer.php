<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use IntlBreakIterator;
use IntlChar;
use IntlCodePointBreakIterator;
use Transliterator;

use function array_map;
use function array_shift;
use function assert;
use function count;
use function explode;
use function implode;
use function in_array;
use function lcfirst;
use function ord;
use function preg_match;
use function preg_replace;
use function str_ends_with;
use function str_replace;
use function str_split;
use function str_starts_with;
use function strtolower;
use function substr;
use function trim;
use function ucfirst;

/**
 * Utility for generating valid PHP labels from UTF-8 strings
 *
 * @internal
 *
 * @see \KynxTest\CodeUtils\AbstractNormalizerTest
 */
abstract class AbstractNormalizer implements NormalizerInterface
{
    private const VALID_CASES = [
        self::CAMEL_CASE,
        self::PASCAL_CASE,
        self::SNAKE_CASE,
        self::UPPER_SNAKE,
    ];

    /**
     * @see https://www.php.net/manual/en/reserved.keywords.php
     * @see https://www.php.net/manual/en/reserved.other-reserved-words.php
     */
    protected const RESERVED = [
        'abstract',
        'and',
        'array',
        'as',
        'bool',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'endswitch',
        'enum',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'false',
        'final',
        'finally',
        'float',
        'fn',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'int',
        'interface',
        'isset',
        'iterable',
        'list',
        'match',
        'mixed',
        'namespace',
        'never',
        'new',
        'null',
        'numeric',
        'object',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'readonly',
        'require',
        'require_once',
        'resource',
        'return',
        'static',
        'string',
        'switch',
        'throw',
        'trait',
        'true',
        'try',
        'unset',
        'use',
        'var',
        'void',
        'while',
        'xor',
        'yield',
    ];

    private const ASCII_SPELLOUT = [
        1   => 'StartOfHeader',
        2   => 'StartOfText',
        3   => 'EndOfText',
        4   => 'EndOfTransmission',
        5   => 'Enquiry',
        6   => 'Acknowledgement',
        7   => 'Bell',
        8   => 'Backspace',
        9   => 'HorizontalTab',
        10  => 'LineFeed',
        11  => 'VerticalTab',
        12  => 'FormFeed',
        13  => 'CarriageReturn',
        14  => 'ShiftOut',
        15  => 'ShiftIn',
        16  => 'DataLinkEscape',
        17  => 'DeviceControlOne',
        18  => 'DeviceControlTwo',
        19  => 'DeviceControlThree',
        20  => 'DeviceControlFour',
        21  => 'NegativeAcknowledgement',
        22  => 'SynchronousIdle',
        23  => 'EndOfTransmissionBlock',
        24  => 'Cancel',
        25  => 'EndOfMedium',
        26  => 'Substitute',
        27  => 'Escape',
        28  => 'FileSeparator',
        29  => 'GroupSeparator',
        30  => 'RecordSeparator',
        31  => 'UnitSeparator',
        32  => 'Space',
        33  => 'Exclamation',
        34  => 'DoubleQuote',
        35  => 'Number',
        36  => 'Dollar',
        37  => 'Percent',
        38  => 'Ampersand',
        39  => 'Quote',
        40  => 'OpenBracket',
        41  => 'CloseBracket',
        42  => 'Asterisk',
        43  => 'Plus',
        44  => 'Comma',
        46  => 'FullStop',
        47  => 'Slash',
        58  => 'Colon',
        59  => 'Semicolon',
        60  => 'LessThan',
        61  => 'Equals',
        62  => 'GreaterThan',
        63  => 'QuestionMark',
        64  => 'At',
        91  => 'OpenSquare',
        92  => 'Backslash',
        93  => 'CloseSquare',
        94  => 'Caret',
        96  => 'Backtick',
        123 => 'OpenCurly',
        124 => 'Pipe',
        125 => 'CloseCurly',
        126 => 'Tilde',
        127 => 'Delete',
    ];

    private const DIGIT_SPELLOUT = [
        48 => 'Zero',
        49 => 'One',
        50 => 'Two',
        51 => 'Three',
        52 => 'Four',
        53 => 'Five',
        54 => 'Six',
        55 => 'Seven',
        56 => 'Eight',
        57 => 'Nine',
    ];

    private Transliterator $latinAscii;
    private IntlCodePointBreakIterator $codePoints;
    private string $case;
    private string|null $suffix;
    private string $separators;

    public function __construct(string|null $suffix, string $case, string $separators = self::DEFAULT_SEPARATORS)
    {
        if (! in_array($case, self::VALID_CASES)) {
            throw NormalizerException::invalidCase($case, self::VALID_CASES);
        }

        $latinAscii = Transliterator::create('NFC; Any-Latin; Latin-ASCII;');
        assert($latinAscii instanceof Transliterator);

        $this->latinAscii = $latinAscii;
        $this->codePoints = IntlBreakIterator::createCodePointInstance();

        $this->case       = $case;
        $this->suffix     = $this->prepareSuffix($suffix, $case);
        $this->separators = str_replace('/', '\\/', $separators);
    }

    protected function toAscii(string $string): string
    {
        $transliterated = $this->latinAscii->transliterate($string);
        if ($transliterated === false) {
            throw NormalizerException::transliterationFailure($string, $this->latinAscii->getErrorMessage());
        }

        $original = explode(' ', $string);
        $words    = explode(' ', $transliterated);
        foreach ($words as $i => $word) {
            // Latin-ASCII tacks a quote to the end of words which contain a combining accent
            if (str_ends_with($word, "'") && ! str_ends_with($original[$i], "'")) {
                $words[$i] = substr($word, 0, -1);
            }
        }

        return $this->spellOutNonAscii(implode(' ', $words));
    }

    protected function separatorsToUnderscore(string $string): string
    {
        return preg_replace('/[' . $this->separators . '\s]+/', '_', trim($string));
    }

    protected function spellOutAscii(string $string): string
    {
        $chunks = str_split($string);
        $last   = count($chunks) - 1;
        foreach (str_split($string) as $i => $char) {
            if (isset(self::ASCII_SPELLOUT[ord($char)])) {
                $char = self::ASCII_SPELLOUT[ord($char)] . ($i < $last ? '_' : '');
            }
            $chunks[$i] = $char;
        }

        return $this->spellOutLeadingDigits(implode('', $chunks));
    }

    protected function toCase(string $string): string
    {
        assert(in_array($this->case, self::VALID_CASES));

        $parts = explode('_', $string);
        return match ($this->case) {
            self::CAMEL_CASE  => $this->toCamelCase($parts),
            self::PASCAL_CASE => $this->toPascalCase($parts),
            self::SNAKE_CASE  => $this->toSnakeCase($parts),
            self::UPPER_SNAKE => $this->toUpperSnake($parts),
        };
    }

    protected function sanitizeReserved(string $string, array $reserved): string
    {
        assert($this->suffix !== null);

        if (in_array(strtolower($string), $reserved, true)) {
            return $string . $this->suffix;
        }
        return $string;
    }

    private function prepareSuffix(string|null $suffix, string $case): string|null
    {
        if ($suffix === null) {
            return $suffix;
        }

        if ($suffix === '' || ! preg_match('/^[a-zA-Z0-9_\x80-\xff]*$/', $suffix)) {
            throw NormalizerException::invalidSuffix($suffix);
        }

        if (! in_array($case, [self::SNAKE_CASE, self::UPPER_SNAKE], true)) {
            return $suffix;
        }
        return str_starts_with($suffix, '_') ? $suffix : '_' . $suffix;
    }

    private function spellOutNonAscii(string $string): string
    {
        $speltOut = '';

        $this->codePoints->setText($string);
        /** @var string $char */
        foreach ($this->codePoints->getPartsIterator() as $char) {
            $ord       = IntlChar::ord($char);
            $speltOut .= $ord < 256 ? $char : $this->spellOutNonAsciiChar($ord);
        }

        return $speltOut;
    }

    private function spellOutNonAsciiChar(int $ord): string
    {
        $speltOut = IntlChar::charName($ord);

        // 'EURO SIGN' -> 'Euro'
        return implode('', array_map(function (string $part): string {
            return $part === 'SIGN' ? '' : ucfirst(strtolower($part));
        }, explode(" ", $speltOut)));
    }

    private function spellOutLeadingDigits(string $string): string
    {
        $chunks = str_split($string);
        foreach ($chunks as $i => $char) {
            if ($i > 1 && $char === '_') {
                $chunks[$i] = '';
                break;
            }

            $ord = ord($char);
            if (! isset(self::DIGIT_SPELLOUT[$ord])) {
                break;
            }

            $chunks[$i] = self::DIGIT_SPELLOUT[$ord] . '_';
        }

        return implode('', $chunks);
    }

    /**
     * @param list<string> $parts
     */
    private function toCamelCase(array $parts): string
    {
        $first = lcfirst(array_shift($parts));
        return $first . $this->toPascalCase($parts);
    }

    /**
     * @param list<string> $parts
     */
    private function toPascalCase(array $parts): string
    {
        return implode('', array_map('ucfirst', $parts));
    }

    /**
     * @param list<string> $parts
     */
    private function toSnakeCase(array $parts): string
    {
        return implode('_', array_map('strtolower', $parts));
    }

    /**
     * @param list<string> $parts
     */
    private function toUpperSnake(array $parts): string
    {
        return implode('_', array_map('strtoupper', $parts));
    }
}
