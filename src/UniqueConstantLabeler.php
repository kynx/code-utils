<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

use Kynx\Code\Normalizer\UniqueStrategy\UniqueStrategyInterface;

/**
 * @see \KynxTest\Code\Normalizer\UniqueConstantLabellerTest
 */
final class UniqueConstantLabeler extends AbstractUniqueLabeler
{
    public function __construct(ConstantNameNormalizer $normalizer, UniqueStrategyInterface $strategy)
    {
        parent::__construct($normalizer, $strategy, true);
    }
}
