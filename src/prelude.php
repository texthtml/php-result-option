<?php

declare(strict_types=1);

namespace th\Option {

    use th\Option;

    /**
     * @template T
     * @param T $value
     * @return Option<T>
     */
    function some(mixed $value): Option
    {
        return \th\Option::some($value);
    }

    /**
     * @template T
     * @return Option<T>
     */
    function none(): Option
    {
        return \th\Option::none();
    }
}

namespace th\Result {

    use th\Result;

    /**
     * @template T
     * @template E
     * @param T $value
     * @return Result<T,E>
     */
    function ok(mixed $value): Result
    {
        return \th\Result::ok($value);
    }

    /**
     * @template T
     * @template E
     * @param E $error
     * @return Result<T,E>
     */
    function error(mixed $error): Result
    {
        return \th\Result::error($error);
    }
}
