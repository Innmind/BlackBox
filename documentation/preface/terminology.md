# Terminology

## Test

A _test_ is a name with a function where you call a part of your program with hardcoded inputs and verify the expected output.

If you've used other PHP testing frameworks there is nothing new here.

## Set

A _Set_ (1) represents any value that BlackBox can generate. For example [`Set::integers()->between(-100, 100)`](../sets.md) represents all the values from `-100` to `100`. You can think of it as `range(-100, 100)`. Except values are not pre-calculated. BlackBox will peak values randonly in the range.
{.annotate}

1. Described by the `Innmind\BlackBox\Set` class.

Sets can describe primitive values as well as complex object trees.

## Proof

A _proof_ is like similar to a _test_ except the inputs are not hardcoded. It uses `Set`s to generate inputs.

For example to prove the correctness of the math `add` (1) function you need to validate it is commutative. With a test you could verify that `add(1, 2)` returns `3` and `add(2, 1)` also returns `3`. This is easy to understand, but it doesn't make sure it works for every value.
{.annotate}

1. This function doesn't exist.

With a _proof_ you can write:

> for any integer $a and any other integer $b then add($a, $b) is identical to add($b, $a)

!!! success ""
    Every time you run such _proof_ it will peak new values from the `Set`s to verify your code.

    This means that the more you run your proofs the greater the confidence your program is correct.

    And the more your team grows the more scenarii will be run.

Using this technique to prove a function as simple as `add` is debatable. But the power of this approach is that the framework will try to find edge cases inputs. For `add` there's not many. But your program certainly has much more.

!!! warning ""
    When you only use tests, if the suite is _green_ you think "everything works".

    With proofs, if the suite is _green_ this means "everything works _so far_". It helps keep the mentality that bugs may still be present in your program. It's a virtuous circle as it motivates to write more tests.

??? abstract
    BlackBox uses the term _proof_ to emphasize that you are testing behaviours not specific scenarii, but these are **NOT** [formal proofs](https://en.wikipedia.org/wiki/Formal_proof).

## Property

A _property_ is like a proof except it's represented via an object (1) and tests the behaviour of an object. The object tested is called a Model.
{.annotate}

1. implementing the interface `Innmind\BlackBox\Property`

Properties come in groups. The framework generates many list of instances of these properties to make sure the Model behaviour is never broken.

For example to prove the correctness of a `Stream` object (1) you can have the properties:
{.annotate}

1. abstraction on top of a `resource`

- `Read`
- `Seek`

BlackBox would generate the steps `[Read, Seek]`, `[Seek, Read]`, `[Read, Read, Seek, Read]`, etc...

??? info
    This has been used in the [`innmind/stream`](https://packagist.org/packages/innmind/stream) package. BlackBox found a sequence of properties that would make the stream unusable. The kind of steps that no human would have written.

This technique works as well for low level abstraction as for entire programs (such as an HTTP API).

If you build packages, you can expose these properties. This allows developers to test their implementation of an interface. This way there sure it behaves at you intended.

Innmind uses this approach for packages such as:

- [`innmind/filesystem`](https://innmind.github.io/Filesystem/) used by [`innmind/s3`](https://packagist.org/packages/innmind/s3)
- [`formal/orm`](https://formal-php.github.io/orm/) to test its different adapters

## Shrinking

Shrinking is the killer feature of Property Based Testing.

Because inputs are random, a failing scenario can be quite complex. Often it's the presence of some characters in a very long string that will make your program break.

Finding the problematic value(s) is not always straightforward.

When BlackBox finds a failing scenario it will _shrink_ the input values and rerun the proof. It will repeat it until the scenario passes again. It then prints the smallest possible values that make the proof fail.

Concretely this means:

- for `string`s it removes characters
- for `integer`s it reduces them toward `0`
- for `array`s it removes elements and then shrink its values
- etc...
