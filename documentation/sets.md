# Discovering `Set`s

Before diving into [proofs](proof.md) you first need to understand the way to describe the _range_ of data that will be fed to your proofs.

If we take back the example of some `add` function that accepts 2 arguments of type `int` you need a way to describe all the `int`s that can be used for this function. This is what is called a `Set`.

Each `Set` can be altered via 2 methods `map` and `filter`. If you want only squares for your proof you would use `Integers::any()->map(static fn($i) => $i ** 2)` or if you want only even integers `Integers::any()->filter(static fn($i) => $i % 2 === 0)`.

BlackBox comes built-in with a wide range of `Set`s (but you can find more on [Packagist](https://packagist.org/providers/innmind/black-box-sets)).

- Primitives
    - [`Innmind\BlackBox\Set\Chars`](../src/Set/Chars.php)
    - [`Innmind\BlackBox\Set\Integers`](../src/Set/Integers.php)
    - [`Innmind\BlackBox\Set\IntegersExceptZero`](../src/Set/IntegersExceptZero.php)
    - [`Innmind\BlackBox\Set\NaturalNumbers`](../src/Set/NaturalNumbers.php)
    - [`Innmind\BlackBox\Set\NaturalNumbersExceptZero`](../src/Set/NaturalNumbersExceptZero.php)
    - [`Innmind\BlackBox\Set\Nullable`](../src/Set/Nullable.php)
    - [`Innmind\BlackBox\Set\RealNumbers`](../src/Set/RealNumbers.php)
    - [`Innmind\BlackBox\Set\Strings`](../src/Set/Strings.php)
    - [`Innmind\BlackBox\Set\Type`](../src/Set/Type.php)
    - [`Innmind\BlackBox\Set\Unicode`](../src/Set/Unicode.php)
    - [`Innmind\BlackBox\Set\UnsafeStrings`](../src/Set/UnsafeStrings.php)
- User defined values
    - [`Innmind\BlackBox\Set\Element`](../src/Set/Element.php)
    - [`Innmind\BlackBox\Set\FromGenerator`](../src/Set/FromGenerator.php)
- Higher order `Set`s
    - [`Innmind\BlackBox\Set\Decorate`](../src/Set/Decorate.php)
    - [`Innmind\BlackBox\Set\Composite`](../src/Set/Composite.php)
    - [`Innmind\BlackBox\Set\Either`](../src/Set/Either.php)
    - [`Innmind\BlackBox\Set\Sequence`](../src/Set/Sequence.php)
- Specific types
    - [`Innmind\BlackBox\Set\Email`](../src/Set/Email.php)
    - [`Innmind\BlackBox\Set\Uuid`](../src/Set/Uuid.php)

## Primitives

### Chars

This `Set` can generate strings containing a single character.

- `Chars::any()` describe any chars that can be returned by the `\chr()` function
- `Chars::lowercaseLetter()` describe the range `a..z`
- `Chars::uppercaseLetter()` describe the range `A..Z`
- `Chars::number()` describe the range `0..9`
- `Chars::ascii()` describe any character that you can typically find on your keyboard
- `Chars::alphanumerical()` describe any character from `::lowercaseLetter()`, `::uppercaseLetter()` or `::number()`

### Integers

- `Integers::any()` describe any integer between `\PHP_INT_MIN` and `\PHP_INT_MAX`
- `Integers::between(min, max)` describe any integer between the bounds you specify
- `Integers::above(min)`
- `Integers::below(max)`

> **Note** The bounds are included in the values that can be generated

### IntegersExceptZero

`IntegersExceptZero::any()` describe any integer except `0`

### NaturalNumbers

`NaturalNumbers::any()` is the same as `Integers::above(0)`

### NaturalNumbersExceptZero

`NaturalNumbersExceptZero::any()` is the same as `Integers::above(1)`

### Nullable

`Nullable::of(Set)` describe all the values that can be generated by the `Set` passed as argument and `null`

### RealNumbers

- `RealNumbers::any()` describe any float between `\PHP_INT_MIN` and `\PHP_INT_MAX`
- `RealNumbers::between(min, max)` describe any float between the bounds you specify
- `RealNumbers::above(min)`
- `RealNumbers::below(max)`

> **Note** The bounds are included in the values that can be generated

### Strings

- `Strings::any()` describe any string of a length between `0` and `128` containing any character from `Chars::any()`
- `Strings::between(min, max)` same as `::any()` but you specify the length range
- `Strings::atMost(max)`
- `Strings::atLeast(min)`
- `Strings::madeOf(Set)` describe any string made of the characters you specify (ie `Strings::madeOf(Chars::alphanumerical())`)
    - you can specify the length range via `Strings::madeOf(Set)->between(min, max)`

### Type

`Type::any()` describe any type that is supported by PHP. This is useful to prove a code doesn't depend on the type of its arguments.

### Unicode

- `Unicode::strings()` is the same as `Strings::madeOf(Unicode::any())`
- `Unicode::any()` describe any single unicode character
- `Unicode` provides all the [unicode blocks](https://unicode-table.com/en/blocks/)

### UnsafeStrings

`UnsafeStrings::any()` describe any string that could break your code. You can use this to test the robustness of your code.

## User defined values

### Elements

`Elements::of(...values)` describe all the values that you put in (ie `Elements::of(true, false)` to describe booleans)

### FromGenerator

`FromGenerator::of(callable)` describe values that you will provide via a `Generator`

## Higher order `Set`s

### Decorate

`Decorate::immutable(callable, Set)` is a way to transform the values that can be generated by the given `Set` (ie `Decorate::immutable(\chr(...), Integers::between(0, 255))` describe all the strings that can be generated by `\chr()`)

### Composite

This `Set` allows to aggregate multiple values to a new one. For example let say you have a `User` class, you could desribe it via:

```php
Set\Composite::immutable(
    static fn(string $firstname, string $lastname) => new User($firstname, $lastname),
    Strings::atLeast(1),
    Strings::atLeast(1),
);
```

Any addition `Set` provided will give access to a new argument to the callable passed as first argument.

### Either

You can think of this `Set` as a _OR_. `Either::any(Integers::any(), Strings::any())` describes any integers or strings.

### Sequence

`Sequence::of(Set)` describes a _list_ (an array of consecutive values) of values of the given `Set` type. `Sequence::of(Integers::any())` describes any list of integers.

By default the list contains between `0` and `100` elements, you can change this via `Sequence::of(Set)->between(min, max)`.

> **Note** the bounds are included

## Specific types

### Email

`Email::any()` describes any valid email string

### Uuid

`Uuid::any()` describes any valid UUID