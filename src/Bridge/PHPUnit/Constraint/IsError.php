<?php

declare(strict_types=1);

namespace th\Bridge\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * Constraint that accepts error Results.
 */
final class IsError extends Constraint
{
    public function toString(): string
    {
        return 'is an error';
    }

    protected function matches($other): bool
    {
        return $other->isError();
    }
}
