# texthtml/Option

Type Option represents an optional value: every Option is either Some and contains a value, or None, and does not. Option types have a number of uses:


* Initial values
* Return values for functions that are not defined over their entire input range (partial functions)
* Return value for otherwise reporting simple errors, where None is returned on error
* Optional class properties
* Optional function arguments
* Nullable values

Option<T> is the type used for a value either containing a T or nothing.

## Usage

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
function even(Option $o): Option
{
    return $o->map((int $i) => $i % 2 === 0);
}
```
