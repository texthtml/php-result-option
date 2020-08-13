<?php

declare(strict_types=1);

namespace th\Result;

final class UnusedResultError extends \Exception
{
    public function __construct()
    {
        parent::__construct("Result was dropped without being used");
    }
}
