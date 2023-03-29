<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

use Kynx\Code\Normalizer\UniqueStrategy\UniqueStrategyInterface;

/**
 * @see \KynxTest\Code\Normalizer\UniqueConstantLabellerTest
 */
final class UniqueClassConstantLabeler extends AbstractUniqueLabeler
{
    public function __construct(ClassConstantNameNormalizer $normalizer, UniqueStrategyInterface $strategy)
    {
        parent::__construct($normalizer, $strategy, true);
    }
}
