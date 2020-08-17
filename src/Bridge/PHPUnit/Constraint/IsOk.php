<?php

declare(strict_types=1);

namespace th\Bridge\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * Constraint that accepts Ok Results.
 */
final class IsOk extends Constraint
{
    public function toString(): string
    {
        return 'is ok';
    }

    protected function matches($other): bool
    {
        return $other->isOk();
    }
}
