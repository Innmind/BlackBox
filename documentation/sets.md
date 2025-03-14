---
hide:
    - navigation
---

# `Set`s

So far you've seen hardcoded `Set`s via `Set::of()` and integers one via `Set::integers()`. But there are much more.

??? tip
    You can find more on [Packagist](https://packagist.org/providers/innmind/black-box-sets).

## Common methods

### Map

For all `Set` objects you can transform the generated values with a function of your own.

Let's say the code you want to prove needs a `Password` object. You can wrap strings in your object like this:

```php
use Innmind\BlackBox\Set;

$set = Set::strings()
    ->map(static fn(string $string) => new Password($string));
```

Now if you use `$set` in a proof it will generate instances of `Password` with a random string inside it.

### Flat map

This method allows to generate a `Set` configured from a randomly generated value from another `Set`.

In most cases you'll want to use the [`Set::composite()`](#composite). This method will be useful for advanced cases.

One example is the ability to randomly generate `Set`s with associated data. Let's say you want to randomly generate strings defining types (`int`, `string`, etc...) with an associated `Set` to generate values of this type in order to build an imaginary `Type` class:

```php
use Innmind\BlackBox\{
    Set,
    Set\Seed,
};

$pairs = Set::of(
    ['int', Set::integers()],
    ['string', Set::strings()],
    ['float', Set::realNumbers()],
    ['bool', Set::of(true, false)],
    // etc...
);

$types = $pairs->flatMap(static function(Seed $pair) {
    [$type, $values] = $pair->unwrap();

    return $values->map(static fn($value) => new Type($type, $value));
});
```

The `#!php $types` `Set` could generate the values `#!php new Type('bool', true)`, `#!php new Type('bool', false)`, `#!php new Type('int', 42)`, etc...

This example is simple enough and could be expressed without the use of `flatMap`. Like this:

```php
use Innmind\BlackBox\Set;

$types = Set::either(
    Set::integers()->map(static fn($value) => ['int', $value]),
    Set::strings()->map(static fn($value) => ['string', $value]),
    Set::realNumbers()->map(static fn($value) => ['float', $value]),
    Set::of(true, false)->map(static fn($value) => ['bool', $value]),
)
    ->map(static function($pair) {
        [$type, $value] = $pair;

        return new Type($type, $value);
    });
```

But say that now you want multiple values instead of a single one. With `flatMap` it's straightforward, unlike with the other approach.

=== "`flatMap`"
    ```php hl_lines="17"
    use Innmind\BlackBox\{
        Set,
        Set\Seed,
    };

    $pairs = Set::of(
        ['int', Set::integers()],
        ['string', Set::strings()],
        ['float', Set::realNumbers()],
        ['bool', Set::of(true, false)],
        // etc...
    );

    $types = $pairs->flatMap(static function(Seed $pair) {
        [$type, $values] = $pair->unwrap();

        return Set::sequence($values)->map(
            static fn($value) => new Type($type, $value),
        );
    });
    ```
=== "Alternative"
    ```php hl_lines="4-7"
    use Innmind\BlackBox\Set;

    $types = Set::either(
        Set::sequence(Set::integers())->map(static fn($value) => ['int', $value]),
        Set::sequence(Set::strings())->map(static fn($value) => ['string', $value]),
        Set::sequence(Set::realNumbers())->map(static fn($value) => ['float', $value]),
        Set::sequence(Set::of(true, false))->map(static fn($value) => ['bool', $value]),
    )
        ->map(static function($pair) {
            [$type, $value] = $pair;

            return new Type($type, $value);
        });
    ```

As you can see with `flatMap` you can locally define what you want without having to change the `Set` you rely on. Unlike the alternative where you need to change the initial `Set` and thus impacting any other `Set` that could depend on it.

??? note "Shrinking"
    In the examples above the value passed as argument to the `flatMap` callable can't be [shrunk](preface/terminology.md#shrinking). This is because we call `->unwrap()` to acces to the real value behind the `Seed` object.

    When the `Seed` value is unwrapped, BlackBox can no longer track how it's used and thus can no longer shrink it.

    In the examples above the seeded value can't be shrunk anyway because it comes from user provided values via `Set::of()`.

    A simple example to demonstrate the use case is prefixing a `string` with an `int`:

    ```php
    use Innmind\BlackBox\{
        Set,
        Set\Seed,
    };

    $set = Set::integers()->flatMap(
        static fn(Seed $int) => Set::strings()->map(
            static fn(string $string) => $int->map(
                static fn(int $int) => $int.$string,
            ),
        ),
    );
    ```

    This way BlackBox knows every transformations of a seeded value and re-apply then after shrinking it.

    And you can also compose multiple `Seed`s via the `Seed::flatMap()` method.

??? warning "Randomness"
    By default the `Set` returned by `flatMap` will produce values with the same _seed_ (the callable argument).

    If you want a more wide range of seeded values you should call the `->randomize()` method after `->flatMap()`.

### Filter

To reuse the password example from above. Say that your password needs to contain the character `$`. You can do:

```php
use Innmind\BlackBox\Set;

$set = Set::strings()
    ->filter(static fn(string $string) => \str_contains($string, '$'))
    ->map(static fn(string $string) => new Password($string));
```

??? warning
    This is an example. You should not enforce your passwords to have a specific value in it. The strength is based on length. ([US](https://www.cisa.gov/secure-our-world/use-strong-passwords) and [French](https://cyber.gouv.fr/publications/recommandations-relatives-lauthentification-multifacteur-et-aux-mots-de-passe) recommendations)

### Nullable

If you need a `Set` to also generate a `null` value you can simply do:

```php
$set = $set->nullable();
```

## Primitives

### Strings

=== "With a single character"
    === "Any"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->chars();
        ```

        This describes any chars that can be returned by the `\chr()` function.
    === "Lowercase letter"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->chars()->lowercaseLetter();
        ```

        This describes the range `a..z`.
    === "Uppercase letter"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->chars()->uppercaseLetter();
        ```

        This describes the range `A..Z`.
    === "Number"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->chars()->number();
        ```

        This describes the range `0..9`.
    === "ASCII"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->chars()->ascii();
        ```

        This describes any character that you can typically find on your keyboard.
    === "Alphanumerical"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->chars()->alphanumerical();
        ```

        This describes any character from `->lowercaseLetter()`, `->uppercaseLetter()` or `->number()`.

=== "With a random length"
    === "Any"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings();
        ```

        This describes any string of a length between `0` and `128` containing any character from `#!php Set::strings()->chars()`.
    === "Between"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->between($min, $max);
        ```

        This is the same as `Set::strings()` but you specify the length range.
    === "At most"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->atMost($max);
        ```
    === "At least"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->atLeast($min);
        ```
    === "Made of specific characters"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->madeOf($set);
        ```

        This describes any string made of the characters you specify (ie `#!php Set::strings()->madeOf(Set::strings()->chars()->alphanumerical())`)

        !!! tip ""
            You can specify the length range via `#!php Set::strings()->madeOf(Set)->between(min, max)`.

=== "Unicode"
    === "Character"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->unicode()->char();
        ```

        This describes any single unicode character.
    === "Strings"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->unicode();
        ```

        This is the same as `#!php Se::strings()->madeOf(Set::strings()->unicode()->char())`.
    === "Character from a block"
        ```php
        use Innmind\BlackBox\Set;

        Set::strings()->unicode()->controlCharater();
        Set::strings()->unicode()->basicLatin();
        Set::strings()->unicode()->spacingModifierLetters();
        // etc...
        ```

        This set provides all the [unicode blocks](https://unicode-table.com/en/blocks/) as dedicated methods.
=== "Unsafe"
    ```php
    use Innmind\BlackBox\Set;

    Set::strings()->unsafe();
    ```

    This describes any string that could break your code. You can use this to test the robustness of your code.

### Integers

=== "Any"
    ```php
    use Innmind\BlackBox\Set;

    Set::integers();
    ```

    This describes any integer between `\PHP_INT_MIN` and `\PHP_INT_MAX`.
=== "Between"
    ```php
    use Innmind\BlackBox\Set;

    Set::integers()->between($min, $max);
    ```

    This describes any integer between the bounds you specify.
=== "Above"
    ```php
    use Innmind\BlackBox\Set;

    Set::integers()->above($min);
    ```
=== "Below"
    ```php
    use Innmind\BlackBox\Set;

    Set::integers()->below($max);
    ```
=== "Any except zero"
    ```php
    use Innmind\BlackBox\Set;

    Set::integers()->exceptZero();
    ```
=== "Natural numbers"
    ```php
    use Innmind\BlackBox\Set;

    Set::integers()->naturalNumbers();
    ```

    This is the same as `#!php Set::integers()->above(0)`.
=== "Natural numbers except zero"
    ```php
    use Innmind\BlackBox\Set;

    Set::integers()->naturalNumbersExceptZero();
    ```

    This is the same as `#!php Set::integers()->above(1)`.

!!! note ""
    The bounds are included in the values that can be generated

### Real numbers

=== "Any"
    ```php
    use Innmind\BlackBox\Set;

    Set::realNumbers();
    ```

    This describes any float between `\PHP_INT_MIN` and `\PHP_INT_MAX`.
=== "Between"
    ```php
    use Innmind\BlackBox\Set;

    Set::realNumbers()->between($min, $max);
    ```

    This describes any float between the bounds you specify.
=== "Above"
    ```php
    use Innmind\BlackBox\Set;

    Set::realNumbers()->above($min);
    ```
=== "Below"
    ```php
    use Innmind\BlackBox\Set;

    Set::realNumbers()->below($max);
    ```

!!! note ""
    The bounds are included in the values that can be generated

### Type

```php
use Innmind\BlackBox\Set;

Set::type();
```

This describes any type that is supported by PHP. This is useful to prove a code doesn't depend on the type of its arguments.

## User defined values

### Elements

```php
use Innmind\BlackBox\Set;

Set::of(...$values);
```

This describes all the values that you put in (ie `#!php Set::of(true, false)` to describe booleans).

### From a generator

```php
use Innmind\BlackBox\Set;

Set::generator(static function() {
    yield from $values;
});
```

This describes values that you will provide via a `Generator`.

## Higher order `Set`s

### Composite

This `Set` allows to aggregate multiple values to a new one. Let's say you have a `User` class, you could desribe it via:

```php
Set::compose(
    static fn(string $firstname, string $lastname) => new User(
        $firstname,
        $lastname,
    ),
    Set::strings()->atLeast(1),
    Set::strings()->atLeast(1),
);
```

Any additionnal `Set` provided will give access to a new argument to the callable.

### Either

You can think of this `Set` as an _OR_.

```php
use Innmind\BlackBox\Set;

Set::either(Set::integers(), Set::strings());
```

This describes any integer or string.

### Sequence

`#!php Set::sequence(Set)` describes a _list_ (an array of consecutive values) of values of the given `Set` type.

```php
use Innmind\BlackBox\Set;

Set::sequence(Set::integers());
```

This describes any list of integers.

By default the list contains between `0` and `100` elements, you can change this via `#!php Set::sequence(Set)->between(min, max)`, `->atLeast()` or `->atMost()`.

!!! note ""
    The bounds are included.

### Tuple

This is a special case of `Composite`. Both examples does the same thing.

=== "Tuple"
    ```php
    Set::tuple(
        Set::integers(),
        Set::integers(),
    )
    ```

=== "Composite"
    ```php
    Set::compose(
        static fn(int $a, int $b) => [$a, $b],
        Set::integers(),
        Set::integers(),
    )
    ```

### Call

```php
Set::call(static function() {
    return $someValue;
})
```

This set is useful when building the Model to tests via [properties](getting-started/property.md). If BlackBox [shrinks](preface/terminology.md#shrinking) properties it will call the provided callable at each shrinking step. This allows to get rid of any state inside your Model between each run.

## Specific types

### Email

```php
use Innmind\BlackBox\Set;

Set::email();
```

This describes any valid email string.

### Uuid

```php
use Innmind\BlackBox\Set;

Set::uuid();
```
This describes any valid UUID.
