<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

/**
 * @see \KynxTest\CodeUtils\PropertyNameNormalizerTest
 */
final class PropertyNameNormalizer extends AbstractNormalizer
{
    public function __construct(string $case = self::CAMEL_CASE, string $separators = self::DEFAULT_SEPARATORS)
    {
        parent::__construct(null, $case, $separators);
    }

    /**
     * Returns a valid PHP property name from a UTF-8 string
     *
     * Property names do not have `$` prepended.
     */
    public function normalize(string $label): string
    {
        $ascii       = $this->toAscii($label);
        $underscored = $this->separatorsToUnderscore($ascii);
        $speltOut    = $this->spellOutAscii($underscored);

        return $this->toCase($speltOut);
    }
}
