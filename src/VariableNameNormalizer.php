<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

use function str_starts_with;
use function strtolower;

/**
 * @see \KynxTest\Code\Normalizer\VariableNameNormalizerTest
 */
final class VariableNameNormalizer extends AbstractNormalizer
{
    private string $thisReplacement;

    /**
     * @param string $thisReplacement Variable name to replace `$this` with
     */
    public function __construct(
        string $thisReplacement = '$self',
        WordCase $case = WordCase::Camel,
        string $separators = self::DEFAULT_SEPARATORS
    ) {
        $this->thisReplacement = str_starts_with($thisReplacement, '$') ? $thisReplacement : '$' . $thisReplacement;

        parent::__construct(null, $case, $separators);
    }

    /**
     * Returns a valid PHP variable name from a UTF-8 string
     *
     * Variable names have `$` prepended. If the transliteration results in the variable `$this`, it will be replaced
     * by the `$thisReplacement` argument passed to the constructor.
     */
    public function normalize(string $label): string
    {
        $ascii    = $this->toAscii($label);
        $spaced   = $this->separatorsToSpace($ascii);
        $speltOut = $this->spellOutAscii($spaced);
        $cased    = $this->toCase($speltOut);

        if (strtolower($cased) === 'this') {
            return $this->thisReplacement;
        }
        return '$' . $cased;
    }
}
