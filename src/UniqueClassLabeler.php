<?php

declare(strict_types=1);

namespace Kynx\Code\Normalizer;

use Kynx\Code\Normalizer\UniqueStrategy\UniqueStrategyInterface;

/**
 * @see \KynxTest\Code\Normalizer\UniqueClassLabellerTest
 */
final class UniqueClassLabeler extends AbstractUniqueLabeler
{
    public function __construct(ClassNameNormalizer $normalizer, UniqueStrategyInterface $strategy)
    {
        parent::__construct($normalizer, $strategy, false);
    }
}
