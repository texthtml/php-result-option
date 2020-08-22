<?php

declare(strict_types=1);

namespace th;

use th\Result\ResultError;
use th\Result\UnusedResultError;
use Throwable;
use WeakMap;

/**
 * @template T
 * @template E
 * @implements \IteratorAggregate<T>
 */
final class Result implements \IteratorAggregate
{
    /** @var T */
    private mixed $value;

    /** @var E */
    private mixed $error;

    /** @var WeakMap<Result<mixed,mixed>,bool> */
    private static WeakMap $toBeUsed;

    private function __construct()
    {
        if (!isset(self::$toBeUsed)) {
            // @codeCoverageIgnoreStart
            self::$toBeUsed = new WeakMap();
            // @codeCoverageIgnoreEnd
        }

        self::$toBeUsed[$this] = true;
    }

    public function __destruct()
    {
        if (self::$toBeUsed[$this]) {
            throw new UnusedResultError();
        }
    }

    /**
     * Create an Ok result.

     * @template U
     * @template F
     * @param U $value
     * @return Result<U,F>
     */
    public static function ok(mixed $value): Result
    {
        $result = new Result();

        $result->value = $value;

        return $result;
    }

    /**
     * Create an error result.
     *
     * @template U
     * @template F
     * @param F $error
     * @return Result<U,F>
     */
    public static function error(mixed $error): Result
    {
        $result = new Result();

        $result->error = $error;

        return $result;
    }

    /**
     * Create an Ok result with the result of the function or an error result if throws an exception
     *
     * @param callable(): T $f
     * @return Result<T,\Throwable>
     */
    public static function try(callable $f): Result
    {
        try {
            return self::ok($f());
        } catch (\Throwable $th) {
            return self::error($th);
        }
    }

    /**
     * Returns true if the result is Ok.
     */
    public function isOk(): bool
    {
        static $valueProperty;
        $valueProperty ??= new \ReflectionProperty(self::class, "value");
        $valueProperty->setAccessible(true);

        self::$toBeUsed[$this] = false;

        return $valueProperty->isInitialized($this);
    }

    /**
     * Returns true if the result is an error.
     */
    public function isError(): bool
    {
        return !$this->isOk();
    }

    /**
     * Returns true if the result is an Ok value containing the given value (compared with ==).
     *
     * @param T $value
     */
    public function contains(mixed $value): bool
    {
        return $this->isOk() && $this->value == $value;
    }

    /**
     * Returns true if the result is an Ok value containing the given value (compared with ===).
     *
     * @param T $value
     */
    public function containsSame(mixed $value): bool
    {
        return $this->isOk() && $this->value === $value;
    }

    /**
     * Returns true if the result is an error containing the given value (comapred with ==).
     *
     * @param E $error
     */
    public function containsError(mixed $error): bool
    {
        return !$this->isOk() && $this->error == $error;
    }

    /**
     * Returns true if the result is an error containing the given value (comapred with ===).
     *
     * @param E $error
     */
    public function containsSameError(mixed $error): bool
    {
        return !$this->isOk() && $this->error === $error;
    }

    /**
     * Maps a Result<T,E> to Result<U,E> by applying a function to a contained Ok value, leaving an Err value untouched.
     *
     * @template U
     * @param callable(T): U $f
     * @return Result<U,E>
     */
    public function map(callable $f): Result
    {
        if (!$this->isOk()) {
            return $this;
        }

        return Result::ok(call_user_func($f, $this->value));
    }

    /**
     * Applies a function to the contained value (if Ok), or returns the provided default (if Err).
     *
     * @template U
     * @param callable(T): U $f
     * @param U $default
     * @return U
     */
    public function mapOr(callable $f, mixed $default): mixed
    {
        if (!$this->isOk()) {
            return $default;
        }

        return call_user_func($f, $this->value);
    }

    /**
     * Maps a Result<T,E> to U by applying a function to a contained Ok value,
     * or a fallback function to a contained Err value.
     *
     * @template U
     * @param callable(T): U $f
     * @param callable(E): U $fallback
     * @return U
     */
    public function mapOrElse(callable $f, callable $fallback): mixed
    {
        return $this->isOk()
            ? call_user_func($f, $this->value)
            : call_user_func($fallback, $this->error);
    }

    /**
     * Maps a Result<T,E> to Result<T,F> by applying a function to a contained Err value, leaving an Ok value untouched.
     *
     * @template F
     * @param callable(E): F $f
     * @return Result<T,F>
     */
    public function mapError(callable $f): Result
    {
        if ($this->isOk()) {
            return $this;
        }

        return Result::error(call_user_func($f, $this->error));
    }

    /**
     * Returns res if the result is Ok, otherwise returns the Err value of self.
     *
     * @template U
     * @param Result<U,E> $res
     * @return Result<U,E>
     */
    public function and(Result $res): Result
    {
        if ($this->isOk()) {
            return $res;
        }

        return $this;
    }

    /**
     * Calls op if the result is Ok, otherwise returns the Err value of self.
     *
     * @template U
     * @param callable(T): Result<U,E> $op
     * @return Result<U,E>
     */
    public function andThen(callable $op): Result
    {
        if ($this->isOk()) {
            return call_user_func($op, $this->value);
        }

        return $this;
    }

    /**
     * Calls op if the result is Ok and returns its Ok(result) or an error result if throws an exception,
     * otherwise returns the Err value of self.
     *
     * @template U
     * @template F
     * @param callable(T): U $op
     * @return Result<U,F>
     */
    public function andTry(callable $op): Result
    {
        return $this->andThen(static fn ($value) => Result::try(static fn () => $op($value)));
    }

    /**
     * Calls op if the result is an error and returns its Ok(result) or an error result if throws an exception,
     * otherwise returns the Ok value of self.
     *
     * @template U
     * @template F
     * @param callable(T): U $op
     * @return Result<U,F>
     */
    public function orTry(callable $op): Result
    {
        return $this->orElse(static fn ($error) => Result::try(static fn () => $op($error)));
    }

    /**
     * Returns res if the result is an error, otherwise returns the Ok value of self.
     *
     * @template U
     * @param Result<U,E> $res
     * @return Result<U,E>
     */
    public function or(Result $res): Result
    {
        if (!$this->isOk()) {
            return $res;
        }

        return $this;
    }

    /**
     * Calls op if the result is an error, otherwise returns the Ok value of self.
     *
     * @template F
     * @param callable(E): F $op
     * @return Result<T,F>
     */
    public function orElse(callable $op): Result
    {
        if (!$this->isOk()) {
            return call_user_func($op, $this->error);
        }

        return $this;
    }

    /**
     * Returns the contained Ok value or throws a ResultError with a custom message if result is an error
     *
     * @throws Result\ResultError
     * @return T
     */
    public function expect(string $errorMessage): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        throw new ResultError($errorMessage, 0, $this->error instanceof Throwable ? $this->error : null);
    }

    /**
     * Returns the contained error value or throws a ResultError with a custom message if result is Ok
     *
     * @throws Result\ResultError
     * @return E
     */
    public function expectError(string $errorMessage): mixed
    {
        if (!$this->isOk()) {
            return $this->error;
        }

        throw new ResultError($errorMessage);
    }

    /**
     * Returns the contained Ok value or throws a ResultError if result is an error
     *
     * @return T
     */
    public function unwrap(): mixed
    {
        return $this->expect("result is not ok");
    }

    /**
     * Returns the contained error value or throws a ResultError if result is an error
     *
     * @return E
     */
    public function unwrapError(): mixed
    {
        return $this->expectError("result is not an error");
    }

    /**
     * Returns the contained Ok value or a provided default.
     *
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        return $default;
    }

    /**
     * Returns the contained Ok value or computes it from a closure.
     *
     * @param callable(E): T $f
     * @return T
     */
    public function unwrapOrElse(callable $f): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        return call_user_func($f, $this->error);
    }

    /**
     * Converts from Result<Result<T,E>,E> to Result<T,E>
     *
     * @template T2
     * @template T of Result<T2,E>
     * @return Result<T2,E>
     */
    public function flatten(): Result
    {
        if ($this->isOk()) {
            return $this->value;
        }

        return $this;
    }

    /**
     * Transposes a Result of an Option into an Option of a Result.
     *
     * @return Option<T>
     */
    public function transpose(): Option
    {
        return $this->mapOrElse(
            static fn (Option $option) => $option->mapOrElse(
                static fn ($value) => Option::some(Result::ok($value)),
                static fn () => Option::none(),
            ),
            fn () => Option::some($this),
        );
    }

    /**
     * Converts from Result<T,E> to Option<T>, and discarding the error, if any.
     *
     * @return Option<T>
     */
    public function okValue(): Option
    {
        return $this->mapOrElse(
            static fn ($value) => Option::some($value),
            static fn () => Option::none(),
        );
    }

    /**
     * Converts from Result<T,E> to Option<E>, and discarding the Ok value, if any.
     *
     * @return Option<T>
     */
    public function errorValue(): Option
    {
        return $this->mapOrElse(
            static fn () => Option::none(),
            static fn ($value) => Option::some($value),
        );
    }

    /**
     * @return \Generator<T>
     */
    public function getIterator(): \Generator
    {
        if ($this->isOk()) {
            yield $this->value;
        }
    }

    public function __clone()
    {
        self::$toBeUsed[$this] = true;
    }
}
