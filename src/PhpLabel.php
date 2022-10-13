<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use function preg_match;

enum PhpLabel: string
{
    case Label    = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/u';
    case Prefix   = '/^[a-zA-Z_\x80-\xff]+$/u';
    case Suffix   = '/^[a-zA-Z0-9_\x80-\xff]+$/u';
    case Variable = '/^\$[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/u';

    public function isValid(string $string): bool
    {
        return match ($this) {
            self::Variable => $string !== '$this' && preg_match($this->value, $string) === 1,
            default        => preg_match($this->value, $string) === 1,
        };
    }
}
