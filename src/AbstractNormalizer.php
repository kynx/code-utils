<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use IntlBreakIterator;
use IntlChar;
use IntlCodePointBreakIterator;
use Transliterator;

use function array_filter;
use function array_map;
use function array_shift;
use function array_slice;
use function assert;
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
        1   => 'Start Of Header',
        2   => 'Start Of Text',
        3   => 'End Of Text',
        4   => 'End Of Transmission',
        5   => 'Enquiry',
        6   => 'Acknowledgement',
        7   => 'Bell',
        8   => 'Backspace',
        9   => 'Horizontal Tab',
        10  => 'Line Feed',
        11  => 'Vertical Tab',
        12  => 'Form Feed',
        13  => 'Carriage Return',
        14  => 'Shift Out',
        15  => 'Shift In',
        16  => 'Data Link Escape',
        17  => 'Device Control One',
        18  => 'Device Control Two',
        19  => 'Device Control Three',
        20  => 'Device Control Four',
        21  => 'Negative Acknowledgement',
        22  => 'Synchronous Idle',
        23  => 'End Of Transmission Block',
        24  => 'Cancel',
        25  => 'End Of Medium',
        26  => 'Substitute',
        27  => 'Escape',
        28  => 'File Separator',
        29  => 'Group Separator',
        30  => 'Record Separator',
        31  => 'Unit Separator',
        33  => 'Exclamation',
        34  => 'Double Quote',
        35  => 'Number',
        36  => 'Dollar',
        37  => 'Percent',
        38  => 'Ampersand',
        39  => 'Quote',
        40  => 'Open Bracket',
        41  => 'Close Bracket',
        42  => 'Asterisk',
        43  => 'Plus',
        44  => 'Comma',
        46  => 'Full Stop',
        47  => 'Slash',
        58  => 'Colon',
        59  => 'Semicolon',
        60  => 'Less Than',
        61  => 'Equals',
        62  => 'Greater Than',
        63  => 'Question Mark',
        64  => 'At',
        91  => 'Open Square',
        92  => 'Backslash',
        93  => 'Close Square',
        94  => 'Caret',
        96  => 'Backtick',
        123 => 'Open Curly',
        124 => 'Pipe',
        125 => 'Close Curly',
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

    protected function separatorsToSpace(string $string): string
    {
        return preg_replace('/[' . $this->separators . '\s_]+/', ' ', trim($string));
    }

    protected function spellOutAscii(string $string): string
    {
        $speltOut = [];
        $current  = '';

        foreach (str_split($string) as $char) {
            $ord = ord($char);
            if (! isset(self::ASCII_SPELLOUT[$ord])) {
                $current .= $char;
                continue;
            }

            $speltOut[] = $current;
            $speltOut[] = self::ASCII_SPELLOUT[$ord];
            $current    = '';
        }
        $speltOut[] = $current;

        return $this->spellOutLeadingDigits(implode(' ', $speltOut));
    }

    protected function toCase(string $string): string
    {
        /** @var list<string> $parts */
        $parts = array_filter(explode(' ', $string));
        return match ($this->case) {
            self::CAMEL_CASE  => $this->toCamelCase($parts),
            self::PASCAL_CASE => $this->toPascalCase($parts),
            self::SNAKE_CASE  => $this->toSnakeCase($parts),
            self::UPPER_SNAKE => $this->toUpperSnake($parts),
        };
    }

    protected function sanitizeReserved(string $string): string
    {
        assert($this->suffix !== null);

        if (in_array(strtolower($string), self::RESERVED, true)) {
            return $string . $this->suffix;
        }
        return $string;
    }

    private function prepareSuffix(string|null $suffix, string $case): string|null
    {
        if ($suffix === null) {
            return null;
        }

        if (! preg_match('/^[a-zA-Z0-9_\x80-\xff]+$/', $suffix)) {
            throw NormalizerException::invalidSuffix($suffix);
        }

        if (! in_array($case, [self::SNAKE_CASE, self::UPPER_SNAKE], true)) {
            return $suffix;
        }
        return str_starts_with($suffix, '_') ? $suffix : '_' . $suffix;
    }

    private function spellOutNonAscii(string $string): string
    {
        $speltOut = [];
        $current  = '';

        $this->codePoints->setText($string);
        /** @var string $char */
        foreach ($this->codePoints->getPartsIterator() as $char) {
            $ord = IntlChar::ord($char);
            if ($ord < 256) {
                $current .= $char;
                continue;
            }

            $speltOut[] = $current;
            $speltOut[] = $this->spellOutNonAsciiChar($ord);
            $current    = '';
        }
        $speltOut[] = $current;

        return implode(' ', $speltOut);
    }

    private function spellOutNonAsciiChar(int $ord): string
    {
        $speltOut = IntlChar::charName($ord);

        // 'EURO SIGN' -> 'euro'
        return implode(' ', array_map(function (string $part): string {
            return $part === 'SIGN' ? '' : strtolower($part);
        }, explode(' ', $speltOut)));
    }

    private function spellOutLeadingDigits(string $string): string
    {
        $speltOut = [];
        $chunks   = str_split($string);
        foreach ($chunks as $i => $char) {
            $ord = ord($char);

            if (! isset(self::DIGIT_SPELLOUT[$ord])) {
                $speltOut[] = implode('', array_slice($chunks, $i));
                break;
            }

            $speltOut[] = self::DIGIT_SPELLOUT[$ord];
        }

        return implode(' ', $speltOut);
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
