<?php

declare(strict_types=1);

namespace th;

use th\Option\NoneError;

/**
 * @template T
 */
final class Option implements \IteratorAggregate
{
    /** @var T */
    private mixed $value;

    private static Option $none;

    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Create an empty Option
     *
     * @return Option<T>
     */
    public static function none(): Option
    {
        if (!isset(self::$none)) {
            self::$none = new Option(new class () extends \stdClass {
            });
        }

        return self::$none;
    }

    /**
     * Create an option with a value
     *
     * @template T
     * @param T $value
     * @return Option<T>
     */
    public static function some(mixed $value): Option
    {
        return new Option($value);
    }

    /**
     * Returns true if the option is none
     */
    public function isNone(): bool
    {
        return $this === Option::none();
    }

    /**
     * Returns true if the option contains a value
     */
    public function isSome(): bool
    {
        return $this !== Option::none();
    }

    /**
     * Returns true if the option contains the given value (compared with ==)
     *
     * @param T $value
     */
    public function contains(mixed $value): bool
    {
        return $this->isSome() && $this->value == $value;
    }

    /**
     * Returns true if the option contains the given value (compared with ===)
     *
     * @param T $value
     */
    public function containsSame(mixed $value): bool
    {
        return $this->isSome() && $this->value === $value;
    }

    /**
     * Returns the contained value or throws a NoneError with a custom message if empty
     *
     * @throws Option\NoneError
     * @return T
     */
    public function expect(string $errorMessage): mixed
    {
        if ($this->isNone()) {
            throw new NoneError($errorMessage);
        }

        return $this->value;
    }

    /**
     * Throws a NoneError with a custom message if not empty
     *
     * @throws Option\NoneError
     */
    public function expectNone(string $errorMessage): void
    {
        if ($this->isSome()) {
            throw new NoneError($errorMessage);
        }
    }

    /**
     * Returns the contained value or throws a NoneError if empty
     *
     * @throws NoneError
     * @return T
     */
    public function unwrap(): mixed
    {
        return $this->expect("");
    }

    /**
     * Throws a NoneError if empty
     *
     * @throws NoneError
     */
    public function unwrapNone(): void
    {
        $this->expectNone("");
    }

    /**
     * Returns the contained value or the given value
     *
     * @param T $value
     * @return T
     */
    public function unwrapOr(mixed $value): mixed
    {
        return $this->isSome() ? $this->value : $value;
    }

    /**
     * Returns the contained value or compute it from the given closure
     *
     * @param callable $f
     * @return T
     */
    public function unwrapOrElse(callable $f): mixed
    {
        return $this->isSome() ? $this->value : call_user_func($f);
    }

    /**
     * Maps to another option by applying a function to the contained value
     *
     * @template U
     *
     * @param callable $f
     * @return Option<U>
     */
    public function map(callable $f): Option
    {
        return $this->isSome() ? Option::some(call_user_func($f, $this->value)) : $this;
    }

    /**
     * Applies a function to the contained value (if any), or returns the provided default (if not).
     *
     * @template U
     *
     * @param callable $f
     * @param U $value
     * @return U
     */
    public function mapOr(callable $f, mixed $value): mixed
    {
        return $this->isSome() ? call_user_func($f, $this->value) : $value;
    }

    /**
     * Applies a function to the contained value (if any), or compute a default (if not).
     *
     * @template U
     *
     * @param callable $f
     * @param callable $default
     * @return U
     */
    public function mapOrElse(callable $f, callable $default): mixed
    {
        return $this->isSome() ? call_user_func($f, $this->value) : call_user_func($default);
    }

    public function getIterator(): \Generator
    {
        if ($this->isSome()) {
            yield $this->value;
        }
    }

    /**
     * Returns None if the option is None, otherwise returns option b.
     *
     * @param Option<T> $b
     * @return Option<T>
     */
    public function and(Option $b): Option
    {
        return $this->isSome() ? $b : $this;
    }

    /**
     * Returns None if the option is None, otherwise calls f with the wrapped value and returns the result
     *
     * @param callable $f
     * @return Option<T>
     */
    public function andThen(callable $f): Option
    {
        return $this->isSome() ? call_user_func($f, $this->value) : $this;
    }

    /**
     * Returns the option if it contains a value, otherwise returns option b.
     *
     * @param Option<T> $b
     * @return Option<T>
     */
    public function or(Option $b): Option
    {
        return $this->isSome() ? $this : $b;
    }

    /**
     * Returns the option if it contains a value, otherwise calls f and returns the result.
     *
     * @param callable $f
     * @return Option<T>
     */
    public function orElse(callable $f): Option
    {
        return $this->isSome() ? $this : call_user_func($f);
    }

    /**
     * Returns Some if exactly one of this and option b is Some, otherwise returns None.
     *
     * @param Option<T> $b
     * @return Option<T>
     */
    public function xor(Option $b): Option
    {
        if ($this->isNone()) {
            return $b;
        }

        if ($b->isNone()) {
            return $this;
        }

        return Option::none();
    }

    /**
     * Returns None if the option is None, otherwise calls predicate with the wrapped value and returns:
     *
     * * Some(t) if predicate returns true (where t is the wrapped value), and
     * * None if predicate returns false.
     *
     * @param callable $p
     * @return Option<T>
     */
    public function filter(callable $p): Option
    {
        return $this->isSome() && call_user_func($p, $this->value) ? $this : Option::none();
    }

    /**
     * Converts from Option<Option<T>> to Option<T>
     *
     * @return Option<T>
     */
    public function flatten(): Option
    {
        return $this->unwrapOr(Option::none());
    }

    /**
     * Zips self with another Option.
     *
     * If self is Some(s) and other is Some(o), this method returns Some([s, o])). Otherwise, None is returned
     *
     * @param Option<U> $b
     * @return Option<V>
     */
    public function zip(Option $b): Option
    {
        return $this->andThen(static fn ($s) => $b->map(static fn ($o) => [$s, $o]));
    }

    /**
     * Zips self with another Option with function f
     *
     * If self is Some(s) and other is Some(o), this method returns Some(f(s, o))). Otherwise, None is returned
     *
     * @param Option<U> $b
     * @param callable $f
     * @return Option<V>
     */
    public function zipWith(Option $b, callable $f): Option
    {
        return $this->andThen(static fn ($s) => $b->map(static fn ($o) => $f($s, $o)));
    }

    /**
     * Transforms the Option<T> into a Result<T,E>, mapping Some(v) to Ok(v) and None to Err(err).
     *
     * @template E
     * @param E $error
     * @return Result<T,E>
     */
    public function okOr(mixed $error): Result
    {
        return $this->mapOrElse(static fn ($value) => Result::ok($value), static fn () => Result::error($error));
    }

    /**
     * Transforms the Option<T> into a Result<T,E>, mapping Some(v) to Ok(v) and None to Err(error()).
     *
     * @template E
     * @param callable $error
     * @return Result<T,E>
     */
    public function okOrElse(callable $error): Result
    {
        return $this->mapOrElse(
            static fn ($value) => Result::ok($value),
            static fn () => Result::error(call_user_func($error)),
        );
    }

    /**
     * Transposes an Option of a Result into a Result of an Option.
     *
     * @template T2
     * @template E
     * @return Result<T2,E>
     */
    public function transpose(): Result
    {
        return $this->mapOrElse(
            static fn (Result $result) => $result->mapOrElse(
                static fn ($value) => Result::ok(Option::some($value)),
                static fn ($error) => Result::error($error),
            ),
            static fn () => Result::ok(Option::none()),
        );
    }
}
