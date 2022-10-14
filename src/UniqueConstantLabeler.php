<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use Kynx\CodeUtils\UniqueStrategy\UniqueStrategyInterface;

/**
 * @see \KynxTest\CodeUtils\UniqueConstantLabellerTest
 */
final class UniqueConstantLabeler extends AbstractUniqueLabeler
{
    public function __construct(ConstantNameNormalizer $normalizer, UniqueStrategyInterface $strategy)
    {
        parent::__construct($normalizer, $strategy, true);
    }
}
