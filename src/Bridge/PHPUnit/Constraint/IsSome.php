<?php

declare(strict_types=1);

namespace th\Bridge\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * Constraint that accepts options that contains a value.
 */
final class IsSome extends Constraint
{
    public function toString(): string
    {
        return 'is some';
    }

    protected function matches($other): bool
    {
        return $other->isSome();
    }
}
