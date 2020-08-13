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
 */
final class Result implements \IteratorAggregate
{
    /** @var T|E */
    private mixed $value;

    private bool $ok;

    private static WeakMap $toBeUsed;

    /**
     * @param T|E $value
     */
    private function __construct(mixed $value, bool $ok)
    {
        $this->value = $value;
        $this->ok = $ok;

        if (!isset(self::$toBeUsed)) {
            self::$toBeUsed = new WeakMap();
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
     * @template T
     * @template E
     * @param T $value
     * return Result<T,E>
     */
    public static function ok(mixed $value): Result
    {
        return new Result($value, true);
    }

    /**
     * @template T
     * @template E
     * @param E $error
     * return Result<T,E>
     */
    public static function error(mixed $error): Result
    {
        return new Result($error, false);
    }

    /**
     * Returns true if the result is Ok.
     */
    public function isOk(): bool
    {
        self::$toBeUsed[$this] = false;

        return $this->ok;
    }

    /**
     * Returns true if the result is an error.
     */
    public function isError(): bool
    {
        self::$toBeUsed[$this] = false;

        return !$this->ok;
    }

    /**
     * Returns true if the result is an Ok value containing the given value (compared with ==).
     *
     * @param T $value
     */
    public function contains(mixed $value): bool
    {
        self::$toBeUsed[$this] = false;

        return $this->ok && $this->value == $value;
    }

    /**
     * Returns true if the result is an Ok value containing the given value (compared with ===).
     *
     * @param T $value
     */
    public function containsSame(mixed $value): bool
    {
        self::$toBeUsed[$this] = false;

        return $this->ok && $this->value === $value;
    }

    /**
     * Returns true if the result is an error containing the given value (comapred with ==).
     *
     * @param E $error
     */
    public function containsError(mixed $error): bool
    {
        self::$toBeUsed[$this] = false;

        return !$this->ok && $this->value == $error;
    }

    /**
     * Returns true if the result is an error containing the given value (comapred with ===).
     *
     * @param E $error
     */
    public function containsSameError(mixed $error): bool
    {
        self::$toBeUsed[$this] = false;

        return !$this->ok && $this->value === $error;
    }

    /**
     * Maps a Result<T,E> to Result<U,E> by applying a function to a contained Ok value, leaving an Err value untouched.
     *
     * @template U
     * @param callable $f
     * @return Result<U,E>
     */
    public function map(callable $f): Result
    {
        self::$toBeUsed[$this] = false;

        if (!$this->ok) {
            return $this;
        }

        return Result::ok(call_user_func($f, $this->value));
    }

    /**
     * Applies a function to the contained value (if Ok), or returns the provided default (if Err).
     *
     * @template U
     * @param callable $f
     * @param U $default
     * @return U
     */
    public function mapOr(callable $f, mixed $default): mixed
    {
        self::$toBeUsed[$this] = false;

        if (!$this->ok) {
            return $default;
        }

        return call_user_func($f, $this->value);
    }

    /**
     * Maps a Result<T,E> to U by applying a function to a contained Ok value,
     * or a fallback function to a contained Err value.
     *
     * @template U
     * @param callable $f
     * @param callable $fallback
     * @return U
     */
    public function mapOrElse(callable $f, callable $fallback): mixed
    {
        self::$toBeUsed[$this] = false;

        return call_user_func($this->ok ? $f : $fallback, $this->value);
    }

    /**
     * Maps a Result<T,E> to Result<T,F> by applying a function to a contained Err value, leaving an Ok value untouched.
     *
     * @template F
     * @param callable $f
     * @return Result<T,F>
     */
    public function mapError(callable $f): Result
    {
        self::$toBeUsed[$this] = false;

        if ($this->ok) {
            return $this;
        }

        return Result::error(call_user_func($f, $this->value));
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
        self::$toBeUsed[$this] = false;

        if ($this->ok) {
            return $res;
        }

        return $this;
    }

    /**
     * Calls op if the result is Ok, otherwise returns the Err value of self.
     *
     * @template U
     * @param callable $op
     * @return Result<U,E>
     */
    public function andThen(callable $op): Result
    {
        self::$toBeUsed[$this] = false;

        if ($this->ok) {
            return call_user_func($op, $this->value);
        }

        return $this;
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
        self::$toBeUsed[$this] = false;

        if (!$this->ok) {
            return $res;
        }

        return $this;
    }

    /**
     * Calls op if the result is an error, otherwise returns the Ok value of self.
     *
     * @template U
     * @param callable $op
     * @return Result<U,E>
     */
    public function orElse(callable $op): Result
    {
        self::$toBeUsed[$this] = false;

        if (!$this->ok) {
            return call_user_func($op, $this->value);
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
        self::$toBeUsed[$this] = false;

        if ($this->ok) {
            return $this->value;
        }

        throw new ResultError($errorMessage, 0, $this->value instanceof Throwable ? $this->value : null);
    }

    /**
     * Returns the contained error value or throws a ResultError with a custom message if result is Ok
     *
     * @throws Result\ResultError
     * @return E
     */
    public function expectError(string $errorMessage): mixed
    {
        self::$toBeUsed[$this] = false;

        if (!$this->ok) {
            return $this->value;
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
        self::$toBeUsed[$this] = false;

        return $this->expect("");
    }

    /**
     * Returns the contained error value or throws a ResultError if result is an error
     *
     * @return E
     */
    public function unwrapError(): mixed
    {
        self::$toBeUsed[$this] = false;

        return $this->expectError("");
    }

    /**
     * Returns the contained Ok value or a provided default.
     *
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed
    {
        self::$toBeUsed[$this] = false;

        if ($this->ok) {
            return $this->value;
        }

        return $default;
    }

    /**
     * Returns the contained Ok value or computes it from a closure.
     *
     * @param callable $f
     * @return T
     */
    public function unwrapOrElse(callable $f): mixed
    {
        self::$toBeUsed[$this] = false;

        if ($this->ok) {
            return $this->value;
        }

        return call_user_func($f, $this->value);
    }

    /**
     * Converts from Result<Result<T,E>,E> to Result<T,E>
     *
     * @template T2
     * @return Result<T2,E>
     */
    public function flatten(): Result
    {
        self::$toBeUsed[$this] = false;

        return $this->unwrapOr($this);
    }

    /**
     * Transposes a Result of an Option into an Option of a Result.
     */
    public function transpose(): Option
    {
        self::$toBeUsed[$this] = false;

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
     * @return Option<T>
     */
    public function okValue(): Option
    {
        self::$toBeUsed[$this] = false;

        return $this->mapOrElse(
            static fn ($value) => Option::some($value),
            static fn () => Option::none(),
        );
    }

    /**
     * Converts from Result<T,E> to Option<E>, and discarding the Ok value, if any.
     * @return Option<T>
     */
    public function errorValue(): Option
    {
        self::$toBeUsed[$this] = false;

        return $this->mapOrElse(
            static fn () => Option::none(),
            static fn ($value) => Option::some($value),
        );
    }

    public function getIterator(): \Generator
    {
        self::$toBeUsed[$this] = false;

        if ($this->ok) {
            yield $this->value;
        }
    }
}
