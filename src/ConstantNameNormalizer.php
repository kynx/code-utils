<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

/**
 * @see \KynxTest\CodeUtils\ConstantNameNormalizerTest
 */
final class ConstantNameNormalizer extends AbstractNormalizer
{
    public function __construct(
        string $suffix,
        string $case = self::UPPER_SNAKE,
        string $separators = self::DEFAULT_SEPARATORS
    ) {
        parent::__construct($suffix, $case, $separators);
    }

    /**
     * Returns a valid PHP constant name from a UTF-8 string
     */
    public function normalize(string $label): string
    {
        $ascii       = $this->toAscii($label);
        $underscored = $this->separatorsToUnderscore($ascii);
        $speltOut    = $this->spellOutAscii($underscored);
        $cased       = $this->toCase($speltOut);

        return $this->sanitizeReserved($cased, self::RESERVED);
    }
}
