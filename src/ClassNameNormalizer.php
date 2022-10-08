<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use function array_filter;
use function explode;
use function implode;

/**
 * @see \KynxTest\CodeUtils\ClassNameNormalizerTest
 */
final class ClassNameNormalizer extends AbstractNormalizer
{
    public function __construct(
        string $suffix,
        string $case = self::PASCAL_CASE,
        string $separators = self::DEFAULT_SEPARATORS
    ) {
        parent::__construct($suffix, $case, $separators);
    }

    /**
     * Returns a valid PHP FQCN from a UTF-8 string
     */
    public function normalize(string $label): string
    {
        $parts = explode('\\', $label);
        foreach ($parts as $i => $part) {
            $parts[$i] = $this->normalizeLabel($part);
        }

        return implode('\\', array_filter($parts));
    }

    private function normalizeLabel(string $label): string
    {
        $ascii       = $this->toAscii($label);
        $underscored = $this->separatorsToUnderscore($ascii);
        $speltOut    = $this->spellOutAscii($underscored);
        $cased       = $this->toCase($speltOut);

        return $this->sanitizeReserved($cased, self::RESERVED);
    }
}