<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

interface NormalizerInterface
{
    public const string CAMEL_CASE  = 'camelCase';
    public const string PASCAL_CASE = 'PascalCase';
    public const string SNAKE_CASE  = 'snake_case';
    public const string UPPER_SNAKE = 'SNAKE_CASE';

    public const string DEFAULT_SEPARATORS = '-.';

    public function normalize(string $label): string;
}
