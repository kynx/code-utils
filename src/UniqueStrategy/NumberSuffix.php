<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer\UniqueStrategy;

use Kynx\Code\Normalizer\WordCase;

use function assert;
use function ucfirst;

/**
 * @see \KynxTest\Code\Normalizer\UniqueStrategy\NumberSuffixTest
 */
final class NumberSuffix implements UniqueStrategyInterface
{
    private WordCase $case;
    private string $suffix;

    public function __construct(WordCase $case = WordCase::Pascal, string $suffix = '')
    {
        $this->case   = $case;
        $this->suffix = $suffix;
    }

    public function generateUniqueName(string $label, int $occurrence, string $original): string
    {
        assert($label !== '');
        $suffix = $this->case->convert($this->suffix . ' ' . $occurrence);
        return match ($this->case) {
            WordCase::Camel  => $label . ucfirst($suffix),
            WordCase::Pascal => $label . $suffix,
            default          => $label . '_' . $suffix
        };
    }
}
