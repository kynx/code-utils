<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use function array_map;
use function assert;
use function implode;
use function lcfirst;
use function preg_match_all;
use function strlen;
use function strtolower;
use function trim;
use function ucfirst;

/**
 * @see \KynxTest\CodeUtils\WordCaseTest
 */
enum WordCase: string
{
    case Camel      = 'camelCase';
    case LowerSnake = 'snake_case';
    case Pascal     = 'PascalCase';
    case UpperSnake = 'UPPER_SNAKE';

    public function convert(string $string): string
    {
        preg_match_all('/[^\s_]+/u', $string, $matches);
        assert(isset($matches[0]));

        return match ($this) {
            self::Camel      => $this->toCamel($matches[0]),
            self::LowerSnake => $this->toLowerSnake($matches[0]),
            self::Pascal     => $this->toPascal($matches[0]),
            self::UpperSnake => $this->toUpperSnake($matches[0])
        };
    }

    /**
     * @param list<string> $parts
     */
    private function toCamel(array $parts): string
    {
        $ucFirst  = array_map(fn (string $part): string => ucfirst(strtolower($part)), $parts);
        $previous = '';
        foreach ($ucFirst as $i => $part) {
            if ($i === 0) {
                $ucFirst[$i] = lcfirst($part);
            }
            if (strlen($previous) === 1 && strlen($part) === 1) {
                $ucFirst[$i] = strtolower($part);
            }
            $previous = $part;
        }

        return trim(implode('', $ucFirst));
    }

    /**
     * @param list<string> $parts
     */
    private function toLowerSnake(array $parts): string
    {
        return trim(implode('_', array_map('strtolower', $parts)));
    }

    /**
     * @param list<string> $parts
     */
    private function toPascal(array $parts): string
    {
        $ucFirst  = array_map(fn (string $part): string => ucfirst(strtolower($part)), $parts);
        $previous = '';
        foreach ($ucFirst as $i => $part) {
            if (strlen($previous) === 1 && strlen($part) === 1) {
                $ucFirst[$i] = strtolower($part);
            }
            $previous = $part;
        }

        return trim(implode('', $ucFirst));
    }

    /**
     * @param list<string> $parts
     */
    private function toUpperSnake(array $parts): string
    {
        return trim(implode('_', array_map('strtoupper', $parts)));
    }
}
