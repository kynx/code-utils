<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use Kynx\CodeUtils\UniqueStrategy\UniqueStrategyInterface;

/**
 * @see \KynxTest\CodeUtils\UniquePropertyLabellerTest
 */
final class UniquePropertyLabeler extends AbstractUniqueLabeler
{
    public function __construct(PropertyNameNormalizer $normalizer, UniqueStrategyInterface $strategy)
    {
        parent::__construct($normalizer, $strategy, true);
    }
}
