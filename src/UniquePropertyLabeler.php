<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

use Kynx\Code\Normalizer\UniqueStrategy\UniqueStrategyInterface;

/**
 * @see \KynxTest\Code\Normalizer\UniquePropertyLabellerTest
 */
final class UniquePropertyLabeler extends AbstractUniqueLabeler
{
    public function __construct(PropertyNameNormalizer $normalizer, UniqueStrategyInterface $strategy)
    {
        parent::__construct($normalizer, $strategy, true);
    }
}
