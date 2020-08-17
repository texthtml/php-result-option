<?php

declare(strict_types=1);

namespace th\Bridge\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * Constraint that accepts empty options.
 */
final class IsNone extends Constraint
{
    public function toString(): string
    {
        return 'is none';
    }

    protected function matches($other): bool
    {
        return $other->isNone();
    }
}
