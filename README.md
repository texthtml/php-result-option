# texthtml/result-option

Result and Option type for your functions returned values.

Result<T,E> is the type used for returning and propagating errors. It is either an Ok(T), representing success and containing a value, er an Error(E), representing error and containing an error value.

Type Option represents an optional value: every Option is either Some and contains a value, or None, and does not. Option types have a number of uses:

* Initial values
* Return values for functions that are not defined over their entire input range (partial functions)
* Return value for otherwise reporting simple errors, where None is returned on error
* Optional class properties
* Optional function arguments
* Nullable values

Option<T> is the type used for a value either containing a T or nothing.

## Result Usage

[Full API documentation](docs/)

### Declaring and writing a function returning a Result

```php
/**
 * @return Result<string,string>
 */
function readFile(string $path): Result
{
    return match ($content = file_get_contents($path)) {
        false => Result::error("file at $path could not be read"),
        default => Result::ok($content),
    };
}
```

### Using a Result

Checking if the result is an error or not:

```php
$content = readFile("/path/to/file");

if ($content->isError()) die("{$content->unwrapError()}" . PHP_EOL);

echo $content->unwrap();
```

Working on the result:

```php
/**
 * @param Result<string,string>
 * @return Result<int,string>
 */
function length(Result $o): Result
{
    return $o->map("strlen");
}
```

## Option Usage

### Declaring and writing a function returning an Option

```php
/**
 * @return Option<string>
 */
function firstChar(string $s): Option
{
    return match (true) {
        $s === "" => Option::none(),
        default   => Option::value($s[0]),
    };
}
```

### Using an Option value

Checking if the result is an error or not:

```php
$c = firstChar("Option");

if ($c->isNone()) die("There is no first character in the string" . PHP_EOL);

echo "firstChar(\"Option\") = {$c->unwrap()}", PHP_EOL;
```

Working on the result:

```php
/**
 * @param Option<int>
 * @return Option<bool>
 */
function even(Option $o): Option
{
    return $o->map((int $i) => $i % 2 === 0);
}
```
