<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

/**
 * @see \KynxTest\Code\Normalizer\PropertyNameNormalizerTest
 */
final class PropertyNameNormalizer extends AbstractNormalizer
{
    public function __construct(WordCase $case = WordCase::Camel, string $separators = self::DEFAULT_SEPARATORS)
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
        $ascii    = $this->toAscii($label);
        $spaced   = $this->separatorsToSpace($ascii);
        $speltOut = $this->spellOutAscii($spaced);

        return $this->toCase($speltOut);
    }
}
