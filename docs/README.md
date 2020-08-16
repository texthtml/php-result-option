
# Documentation

- [`Result`](#class-thresult)
- [`Option`](#class-thoption)

## `class Result<T,E>`

| Visibility | Function |
|:-----------|:---------|
| public static | <code><strong>ok(</strong><em>T</em> <strong>$value</strong>)</strong>: <em>[`Result`](#class-thresult)<U,F></em><br /></code><em>Create an Ok result.</em> |
| public static | <code><strong>error(</strong><em>E</em> <strong>$error</strong>)</strong>: <em>[`Result`](#class-thresult)<U,F></em><br /></code><em>Create an error result.</em> |
| public | <code><strong>and(</strong><em>[`Result`](#class-thresult)<U,E></em> <strong>$res</strong>)</strong>: <em>[`Result`](#class-thresult)<U,E></em><br /></code><em>Returns res if the result is Ok, otherwise returns the Err value of self.</em> |
| public | <code><strong>andThen(</strong><em>\callable</em> <strong>$op</strong>)</strong>: <em>[`Result`](#class-thresult)<U,E></em><br /></code><em>Calls op if the result is Ok, otherwise returns the Err value of self.</em> |
| public | <code><strong>contains(</strong><em>T</em> <strong>$value</strong>)</strong>: <em>void</em><br /></code><em>Returns true if the result is an Ok value containing the given value (compared with ==).</em> |
| public | <code><strong>containsError(</strong><em>E</em> <strong>$error</strong>)</strong>: <em>void</em><br /></code><em>Returns true if the result is an error containing the given value (comapred with ==).</em> |
| public | <code><strong>containsSame(</strong><em>T</em> <strong>$value</strong>)</strong>: <em>void</em><br /></code><em>Returns true if the result is an Ok value containing the given value (compared with ===).</em> |
| public | <code><strong>containsSameError(</strong><em>E</em> <strong>$error</strong>)</strong>: <em>void</em><br /></code><em>Returns true if the result is an error containing the given value (comapred with ===).</em> |
| public | <code><strong>errorValue()</strong>: <em>[`Option`](#class-thoption)<T></em><br /></code><em>Converts from Result<T,E> to Option<E>, and discarding the Ok value, if any.</em> |
| public | <code><strong>expect(</strong><em>\string</em> <strong>$errorMessage</strong>)</strong>: <em>T</em><br /></code><em>Returns the contained Ok value or throws a ResultError with a custom message if result is an error</em> |
| public | <code><strong>expectError(</strong><em>\string</em> <strong>$errorMessage</strong>)</strong>: <em>E</em><br /></code><em>Returns the contained error value or throws a ResultError with a custom message if result is Ok</em> |
| public | <code><strong>flatten()</strong>: <em>[`Result`](#class-thresult)<T2,E></em><br /></code><em>Converts from Result<Result<T,E>,E> to Result<T,E></em> |
| public | <code><strong>getIterator()</strong>: <em>\Generator<T></em> |
| public | <code><strong>isError()</strong>: <em>bool</em><br /></code><em>Returns true if the result is an error.</em> |
| public | <code><strong>isOk()</strong>: <em>bool</em><br /></code><em>Returns true if the result is Ok.</em> |
| public | <code><strong>map(</strong><em>\callable</em> <strong>$f</strong>)</strong>: <em>[`Result`](#class-thresult)<U,E></em><br /></code><em>Maps a Result<T,E> to Result<U,E> by applying a function to a contained Ok value, leaving an Err value untouched.</em> |
| public | <code><strong>mapError(</strong><em>\callable</em> <strong>$f</strong>)</strong>: <em>[`Result`](#class-thresult)<T,F></em><br /></code><em>Maps a Result<T,E> to Result<T,F> by applying a function to a contained Err value, leaving an Ok value untouched.</em> |
| public | <code><strong>mapOr(</strong><em>\callable</em> <strong>$f</strong>, <em>U</em> <strong>$default</strong>)</strong>: <em>U</em><br /></code><em>Applies a function to the contained value (if Ok), or returns the provided default (if Err).</em> |
| public | <code><strong>mapOrElse(</strong><em>\callable</em> <strong>$f</strong>, <em>\callable</em> <strong>$fallback</strong>)</strong>: <em>U</em><br /></code><em>Maps a Result<T,E> to U by applying a function to a contained Ok value, or a fallback function to a contained Err value.</em> |
| public | <code><strong>okValue()</strong>: <em>[`Option`](#class-thoption)<T></em><br /></code><em>Converts from Result<T,E> to Option<T>, and discarding the error, if any.</em> |
| public | <code><strong>or(</strong><em>[`Result`](#class-thresult)<U,E></em> <strong>$res</strong>)</strong>: <em>[`Result`](#class-thresult)<U,E></em><br /></code><em>Returns res if the result is an error, otherwise returns the Ok value of self.</em> |
| public | <code><strong>orElse(</strong><em>\callable</em> <strong>$op</strong>)</strong>: <em>[`Result`](#class-thresult)<U,E></em><br /></code><em>Calls op if the result is an error, otherwise returns the Ok value of self.</em> |
| public | <code><strong>transpose()</strong>: <em>[`Option`](#class-thoption)<T></em><br /></code><em>Transposes a Result of an Option into an Option of a Result.</em> |
| public | <code><strong>unwrap()</strong>: <em>T</em><br /></code><em>Returns the contained Ok value or throws a ResultError if result is an error</em> |
| public | <code><strong>unwrapError()</strong>: <em>E</em><br /></code><em>Returns the contained error value or throws a ResultError if result is an error</em> |
| public | <code><strong>unwrapOr(</strong><em>T</em> <strong>$default</strong>)</strong>: <em>T</em><br /></code><em>Returns the contained Ok value or a provided default.</em> |
| public | <code><strong>unwrapOrElse(</strong><em>\callable</em> <strong>$f</strong>)</strong>: <em>T</em><br /></code><em>Returns the contained Ok value or computes it from a closure.</em> |

*This class implements `\Traversable`*

## `class Option<T>`

| Visibility | Function |
|:-----------|:---------|
| public static | <code><strong>some(</strong><em>T</em> <strong>$value</strong>)</strong>: <em>[`Option`](#class-thoption)</em><br /></code><em>Create an option with a value</em> |
| public static | <code><strong>none()</strong>: <em>[`Option`](#class-thoption)</em><br /></code><em>Create an empty Option</em> |
| public | <code><strong>and(</strong><em>[`Option`](#class-thoption)</em> <strong>$b</strong>)</strong>: <em>[`Option`](#class-thoption)</em><br /></code><em>Returns None if the option is None, otherwise returns option b.</em> |
| public | <code><strong>andThen(</strong><em>\callable</em> <strong>$f</strong>)</strong>: <em>[`Option`](#class-thoption)</em><br /></code><em>Returns None if the option is None, otherwise calls f with the wrapped value and returns the result</em> |
| public | <code><strong>contains(</strong><em>T</em> <strong>$value</strong>)</strong>: <em>void</em><br /></code><em>Returns true if the option contains the given value (compared with ==)</em> |
| public | <code><strong>containsSame(</strong><em>T</em> <strong>$value</strong>)</strong>: <em>void</em><br /></code><em>Returns true if the option contains the given value (compared with ===)</em> |
| public | <code><strong>expect(</strong><em>\string</em> <strong>$errorMessage</strong>)</strong>: <em>T</em><br /></code><em>Returns the contained value or throws a NoneError with a custom message if empty</em> |
| public | <code><strong>expectNone(</strong><em>\string</em> <strong>$errorMessage</strong>)</strong>: <em>void</em><br /></code><em>Throws a NoneError with a custom message if not empty</em> |
| public | <code><strong>filter(</strong><em>\callable</em> <strong>$p</strong>)</strong>: <em>[`Option`](#class-thoption)<T></em><br /></code><em>Returns None if the option is None, otherwise calls predicate with the wrapped value and returns: * Some(t) if predicate returns true (where t is the wrapped value), and * None if predicate returns false.</em> |
| public | <code><strong>flatten()</strong>: <em>[`Option`](#class-thoption)</em><br /></code><em>Converts from Option<Option<T>> to Option<T></em> |
| public | <code><strong>getIterator()</strong>: <em>\Generator<T></em> |
| public | <code><strong>isNone()</strong>: <em>bool</em><br /></code><em>Returns true if the option is none</em> |
| public | <code><strong>isSome()</strong>: <em>bool</em><br /></code><em>Returns true if the option contains a value</em> |
| public | <code><strong>map(</strong><em>\callable</em> <strong>$f</strong>)</strong>: <em>[`Option`](#class-thoption)</em><br /></code><em>Maps to another option by applying a function to the contained value</em> |
| public | <code><strong>mapOr(</strong><em>\callable</em> <strong>$f</strong>, <em>U</em> <strong>$value</strong>)</strong>: <em>U</em><br /></code><em>Applies a function to the contained value (if any), or returns the provided default (if not).</em> |
| public | <code><strong>mapOrElse(</strong><em>\callable</em> <strong>$f</strong>, <em>\callable</em> <strong>$default</strong>)</strong>: <em>U</em><br /></code><em>Applies a function to the contained value (if any), or compute a default (if not).</em> |
| public | <code><strong>okOr(</strong><em>E</em> <strong>$error</strong>)</strong>: <em>[`Result`](#class-thresult)<T,E></em><br /></code><em>Transforms the Option<T> into a Result<T,E>, mapping Some(v) to Ok(v) and None to Err(err).</em> |
| public | <code><strong>okOrElse(</strong><em>\callable</em> <strong>$error</strong>)</strong>: <em>[`Result`](#class-thresult)<T,E></em><br /></code><em>Transforms the Option<T> into a Result<T,E>, mapping Some(v) to Ok(v) and None to Err(error()).</em> |
| public | <code><strong>or(</strong><em>[`Option`](#class-thoption)<T></em> <strong>$b</strong>)</strong>: <em>[`Option`](#class-thoption)<T></em><br /></code><em>Returns the option if it contains a value, otherwise returns option b.</em> |
| public | <code><strong>orElse(</strong><em>\callable</em> <strong>$f</strong>)</strong>: <em>[`Option`](#class-thoption)<T></em><br /></code><em>Returns the option if it contains a value, otherwise calls f and returns the result.</em> |
| public | <code><strong>transpose()</strong>: <em>[`Result`](#class-thresult)<Option,E></em><br /></code><em>Transposes an Option of a Result into a Result of an Option.</em> |
| public | <code><strong>unwrap()</strong>: <em>T</em><br /></code><em>Returns the contained value or throws a NoneError if empty</em> |
| public | <code><strong>unwrapNone()</strong>: <em>void</em><br /></code><em>Throws a NoneError if empty</em> |
| public | <code><strong>unwrapOr(</strong><em>T</em> <strong>$value</strong>)</strong>: <em>T</em><br /></code><em>Returns the contained value or the given value</em>
| public | <code><strong>unwrapOrElse(</strong><em>\callable</em> <strong>$f</strong>)</strong>: <em>T</em><br /></code><em>Returns the contained value or compute it from the given closure</em> |
| public | <code><strong>xor(</strong><em>[`Option`](#class-thoption)<T></em> <strong>$b</strong>)</strong>: <em>[`Option`](#class-thoption)<T></em><br /></code><em>Returns Some if exactly one of this and option b is Some, otherwise returns None.</em> |
| public | <code><strong>zip(</strong><em>[`Option`](#class-thoption)</em> <strong>$b</strong>)</strong>: <em>[`Option`](#class-thoption)<V></em><br /></code><em>Zips self with another Option. If self is Some(s) and other is Some(o), this method returns Some([s, o])). Otherwise, None is returned</em> |
| public | <code><strong>zipWith(</strong><em>[`Option`](#class-thoption)</em> <strong>$b</strong>, <em>\callable</em> <strong>$f</strong>)</strong>: <em>[`Option`](#class-thoption)<V></em><br /></code><em>Zips self with another Option with function f If self is Some(s) and other is Some(o), this method returns Some(f(s, o))). Otherwise, None is returned</em> |

*This class implements `\Traversable`*
