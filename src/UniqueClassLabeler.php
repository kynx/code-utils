<?php

declare(strict_types=1);

namespace Kynx\CodeUtils;

use Kynx\CodeUtils\UniqueStrategy\UniqueStrategyInterface;

/**
 * @see \KynxTest\CodeUtils\UniqueClassLabellerTest
 */
final class UniqueClassLabeler extends AbstractUniqueLabeler
{
    public function __construct(ClassNameNormalizer $normalizer, UniqueStrategyInterface $strategy)
    {
        parent::__construct($normalizer, $strategy, false);
    }
}
