<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

/**
 * @see \KynxTest\Code\Normalizer\ClassConstantNameNormalizerTest
 */
final class ClassConstantNameNormalizer extends AbstractNormalizer
{
    protected const RESERVED = [
        'class',
    ];

    public function __construct(
        string $suffix,
        WordCase $case = WordCase::UpperSnake,
        string $separators = self::DEFAULT_SEPARATORS
    ) {
        parent::__construct($suffix, $case, $separators);
    }

    /**
     * Returns a valid PHP constant name from a UTF-8 string
     */
    public function normalize(string $label): string
    {
        $ascii    = $this->toAscii($label);
        $spaced   = $this->separatorsToSpace($ascii);
        $speltOut = $this->spellOutAscii($spaced);
        $cased    = $this->toCase($speltOut);

        return $this->sanitizeReserved($cased);
    }
}
