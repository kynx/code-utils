<?php

declare(strict_types=1);

namespace Kynx\CodeUtils\UniqueStrategy;

use Kynx\CodeUtils\WordCase;
use NumberFormatter;

use function ucfirst;

/**
 * @see \KynxTest\CodeUtils\UniqueStrategy\SpellOutOrdinalPrefixTest
 */
final class SpellOutOrdinalPrefix implements UniqueStrategyInterface
{
    private WordCase $case;
    private NumberFormatter $formatter;

    public function __construct(WordCase $case = WordCase::Camel)
    {
        $this->case      = $case;
        $this->formatter = new NumberFormatter('en_US', NumberFormatter::SPELLOUT);
        $this->formatter->setTextAttribute(NumberFormatter::DEFAULT_RULESET, '%spellout-ordinal');
    }

    public function generateUniqueName(string $label, int $occurrence, string $original): string
    {
        $prefix = $this->case->convert($this->formatter->format($occurrence));
        return match ($this->case) {
            WordCase::Camel  => $prefix . ucfirst($label),
            WordCase::Pascal => $prefix . $label,
            default          => $prefix . '_' . $label
        };
    }
}
