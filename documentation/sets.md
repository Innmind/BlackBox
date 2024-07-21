---
hide:
    - navigation
---

# `Set`s

So far you've seen hardcoded `Set`s via `Set\Element` and integers one via `Set\Integers`. But there are much more.

- Primitives
    - [`Innmind\BlackBox\Set\Chars`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Chars.php)
    - [`Innmind\BlackBox\Set\Integers`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Integers.php)
    - [`Innmind\BlackBox\Set\IntegersExceptZero`](https://github.com/Innmind/BlackBox/tree/master/src/Set/IntegersExceptZero.php)
    - [`Innmind\BlackBox\Set\NaturalNumbers`](https://github.com/Innmind/BlackBox/tree/master/src/Set/NaturalNumbers.php)
    - [`Innmind\BlackBox\Set\NaturalNumbersExceptZero`](https://github.com/Innmind/BlackBox/tree/master/src/Set/NaturalNumbersExceptZero.php)
    - [`Innmind\BlackBox\Set\Nullable`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Nullable.php)
    - [`Innmind\BlackBox\Set\RealNumbers`](https://github.com/Innmind/BlackBox/tree/master/src/Set/RealNumbers.php)
    - [`Innmind\BlackBox\Set\Strings`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Strings.php)
    - [`Innmind\BlackBox\Set\Type`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Type.php)
    - [`Innmind\BlackBox\Set\Unicode`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Unicode.php)
    - [`Innmind\BlackBox\Set\UnsafeStrings`](https://github.com/Innmind/BlackBox/tree/master/src/Set/UnsafeStrings.php)
- User defined values
    - [`Innmind\BlackBox\Set\Element`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Element.php)
    - [`Innmind\BlackBox\Set\FromGenerator`](https://github.com/Innmind/BlackBox/tree/master/src/Set/FromGenerator.php)
- Higher order `Set`s
    - [`Innmind\BlackBox\Set\Decorate`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Decorate.php)
    - [`Innmind\BlackBox\Set\Composite`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Composite.php)
    - [`Innmind\BlackBox\Set\Either`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Either.php)
    - [`Innmind\BlackBox\Set\Sequence`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Sequence.php)
    - [`Innmind\BlackBox\Set\Tuple`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Tuple.php)
    - [`Innmind\BlackBox\Set\Call`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Call.php)
- Specific types
    - [`Innmind\BlackBox\Set\Email`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Email.php)
    - [`Innmind\BlackBox\Set\Uuid`](https://github.com/Innmind/BlackBox/tree/master/src/Set/Uuid.php)

??? tip
    You can find more on [Packagist](https://packagist.org/providers/innmind/black-box-sets).

## Common methods

### Map

For all `Set` objects you can transform the generated values with a function of your own.

Let's say the code you want to prove needs a `Password` object. You can wrap strings in your object like this:

```php
use Innmind\BlackBox\Set;

$set = Set\Strings::any()
    ->map(static fn(string $string) => new Password($string));
```

Now if you use `$set` in a proof it will generate instances of `Password` with a random string inside it.

### Filter

To reuse the password example from above. Say that your password needs to contain the character `$`. You can do:

```php
use Innmind\BlackBox\Set;

$set = Set\Strings::any()
    ->filter(static fn(string $string) => \str_contains($string, '$'))
    ->map(static fn(string $string) => new Password($string));
```

??? warning
    This is an example. You should not enforce your passwords to have a specific value in it. The strength is based on length. ([US](https://www.cisa.gov/secure-our-world/use-strong-passwords) and [French](https://cyber.gouv.fr/publications/recommandations-relatives-lauthentification-multifacteur-et-aux-mots-de-passe) recommendations)

## Primitives

### Chars

This `Set` can generate strings containing a single character.

- `#!php Chars::any()` describes any chars that can be returned by the `\chr()` function
- `#!php Chars::lowercaseLetter()` describes the range `a..z`
- `#!php Chars::uppercaseLetter()` describes the range `A..Z`
- `#!php Chars::number()` describes the range `0..9`
- `#!php Chars::ascii()` describes any character that you can typically find on your keyboard
- `#!php Chars::alphanumerical()` describes any character from `::lowercaseLetter()`, `::uppercaseLetter()` or `::number()`

### Integers

- `#!php Integers::any()` describes any integer between `\PHP_INT_MIN` and `\PHP_INT_MAX`
- `#!php Integers::between(min, max)` describes any integer between the bounds you specify
- `#!php Integers::above(min)`
- `#!php Integers::below(max)`

!!! note ""
    The bounds are included in the values that can be generated

### IntegersExceptZero

`#!php IntegersExceptZero::any()` describes any integer except `0`

### NaturalNumbers

`#!php NaturalNumbers::any()` is the same as `#!php Integers::above(0)`

### NaturalNumbersExceptZero

`#!php NaturalNumbersExceptZero::any()` is the same as `#!php Integers::above(1)`

### Nullable

`#!php Nullable::of(Set)` describes all the values that can be generated by the `Set` passed as argument and `null`

### RealNumbers

- `#!php RealNumbers::any()` describes any float between `\PHP_INT_MIN` and `\PHP_INT_MAX`
- `#!php RealNumbers::between(min, max)` describes any float between the bounds you specify
- `#!php RealNumbers::above(min)`
- `#!php RealNumbers::below(max)`

!!! note ""
    The bounds are included in the values that can be generated

### Strings

- `#!php Strings::any()` describes any string of a length between `0` and `128` containing any character from `Chars::any()`
- `#!php Strings::between(min, max)` same as `::any()` but you specify the length range
- `#!php Strings::atMost(max)`
- `#!php Strings::atLeast(min)`
- `#!php Strings::madeOf(Set)` describes any string made of the characters you specify (ie `#!php Strings::madeOf(Chars::alphanumerical())`)
    - you can specify the length range via `#!php Strings::madeOf(Set)->between(min, max)`

### Type

`Type::any()` describes any type that is supported by PHP. This is useful to prove a code doesn't depend on the type of its arguments.

### Unicode

- `#!php Unicode::strings()` is the same as `#!php Strings::madeOf(Unicode::any())`
- `#!php Unicode::any()` describes any single unicode character
- `#!php Unicode` provides all the [unicode blocks](https://unicode-table.com/en/blocks/)

### UnsafeStrings

`#!php UnsafeStrings::any()` describes any string that could break your code. You can use this to test the robustness of your code.

## User defined values

### Elements

`#!php Elements::of(...values)` describes all the values that you put in (ie `#!php Elements::of(true, false)` to describe booleans)

### FromGenerator

`#!php FromGenerator::of(callable)` describes values that you will provide via a `Generator`

## Higher order `Set`s

### Decorate

`#!php Decorate::immutable(callable, Set)` is a way to transform the values that can be generated by the given `Set` (ie `#!php Decorate::immutable(\chr(...), Integers::between(0, 255))` describes all the strings that can be generated by `\chr()`)

This is the same as `Integers::between(0, 255)->map(\chr(...))`.

### Composite

This `Set` allows to aggregate multiple values to a new one. Let's say you have a `User` class, you could desribe it via:

```php
Set\Composite::immutable(
    static fn(string $firstname, string $lastname) => new User(
        $firstname,
        $lastname,
    ),
    Strings::atLeast(1),
    Strings::atLeast(1),
);
```

Any additionnal `Set` provided will give access to a new argument to the callable.

### Either

You can think of this `Set` as an _OR_. `#!php Either::any(Integers::any(), Strings::any())` describes any integer or string.

### Sequence

`#!php Sequence::of(Set)` describes a _list_ (an array of consecutive values) of values of the given `Set` type. `#!php Sequence::of(Integers::any())` describes any list of integers.

By default the list contains between `0` and `100` elements, you can change this via `#!php Sequence::of(Set)->between(min, max)`.

!!! note ""
    The bounds are included.

### Tuple

This is a special case of `Composite`. Both examples does the same thing.

=== "Tuple"
    ```php
    Tuple::of(
        Integers::any(),
        Integers::any(),
    )
    ```

=== "Composite"
    ```php
    Composite::immutable(
        static fn(int $a, int $b) => [$a, $b],
        Integers::any(),
        Integers::any(),
    )
    ```

### Call

```php
Call::of(static function() {
    return $someValue;
})
```

This set is useful when building the Model to tests via [properties](getting-started/property.md). If BlackBox [shrinks](preface/terminology.md#shrinking) properties it will call the provided callable at each shrinking step. This allows to get rid of any state inside your Model between each run.

## Specific types

### Email

`#!php Email::any()` describes any valid email string

### Uuid

`#!php Uuid::any()` describes any valid UUID
