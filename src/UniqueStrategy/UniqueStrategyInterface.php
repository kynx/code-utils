<?php

declare(strict_types=1);

namespace Kynx\CodeUtils\UniqueStrategy;

interface UniqueStrategyInterface
{
    /**
     * Returns unique name for `$label`
     *
     * @param int $occurrence The number of times `$label` has been seen
     * @param string $original The original string, before any normalization
     */
    public function generateUniqueName(string $label, int $occurrence, string $original): string;
}
