<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

use Kynx\Code\Normalizer\UniqueStrategy\UniqueStrategyInterface;

use function array_map;
use function array_search;
use function assert;
use function is_string;
use function strtolower;

/**
 * @see \KynxTest\Code\Normalizer\AbstractUniqueLabellerTest
 */
abstract class AbstractUniqueLabeler
{
    private NormalizerInterface $normalizer;
    private UniqueStrategyInterface $strategy;
    private bool $caseSensitive;

    public function __construct(
        NormalizerInterface $normalizer,
        UniqueStrategyInterface $strategy,
        bool $caseSensitive
    ) {
        $this->normalizer    = $normalizer;
        $this->strategy      = $strategy;
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * Returns an array of unique labels keyed by the original string
     *
     * @param array<array-key, string> $labels
     */
    public function getUnique(array $labels): array
    {
        $occurrences = [];
        $unique      = [];

        foreach ($labels as $label) {
            $normalised = $this->normalizer->normalize($label);
            assert($normalised !== '');
            $key = $this->caseSensitive ? $normalised : strtolower($normalised);

            if (isset($occurrences[$key])) {
                /** @psalm-suppress RedundantCondition */
                if ($occurrences[$key] === 1) {
                    $oldLabel          = $this->arraySearch($normalised, $unique);
                    $unique[$oldLabel] = $this->strategy->generateUniqueName($unique[$oldLabel], 1, $label);
                }

                $occurrences[$key]++;
                $unique[$label] = $this->strategy->generateUniqueName($normalised, $occurrences[$key], $label);
            } else {
                $occurrences[$key] = 1;
                $unique[$label]    = $normalised;
            }
        }

        return $unique;
    }

    /**
     * @param array<string, string> $haystack
     */
    private function arraySearch(string $needle, array $haystack): string
    {
        if ($this->caseSensitive) {
            $key = array_search($needle, $haystack, true);
        } else {
            $key = array_search(strtolower($needle), array_map('strtolower', $haystack));
        }

        assert(is_string($key));
        return $key;
    }
}
