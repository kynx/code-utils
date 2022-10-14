<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

use Kynx\Code\Normalizer\UniqueStrategy\UniqueStrategyInterface;

final class UniqueVariableLabeler extends AbstractUniqueLabeler
{
    public function __construct(VariableNameNormalizer $normalizer, UniqueStrategyInterface $strategy)
    {
        parent::__construct($normalizer, $strategy, true);
    }
}
