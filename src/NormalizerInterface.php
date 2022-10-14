<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

interface NormalizerInterface
{
    public const CAMEL_CASE  = 'camelCase';
    public const PASCAL_CASE = 'PascalCase';
    public const SNAKE_CASE  = 'snake_case';
    public const UPPER_SNAKE = 'SNAKE_CASE';

    public const DEFAULT_SEPARATORS = '-.';

    public function normalize(string $label): string;
}
