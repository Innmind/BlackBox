# Writing your first proof

A proof is described by:

- a name
- [`Set`s](sets.md) to describe the inputs
- a function that test a code and must always be true no matter the values passed as input

If we reuse the fictional `add` function we've used before, one of its proofs is that it is commutative meaning the order of its arguments doesn't change the result value.

The proof is written as follows:

```php
use Innmind\BlackBox\{
    Set,
    Runner\Assert,
};

proof(
    'add is commutative', // this is the name of the proof
    given(
        Set\Integers::any(),
        Set\Integers::any(),
    ),
    static fn(Assert $assert, int $a, int $b) => $assert->same(
        add($a, $b),
        add($b, $a),
    ),
);
```

Each `Set` passed to `given` will add a new argument passed to the callable, in this case we describe that we need 2 integers for our proof.

The `Assert` value passed to the callable is the utility to write your assertions. Here we verify that when calling our `add` function we always have the same value even when swithing the order of the arguments.

Writing this proof is the same as writing all the tests manually for all integers (`same(add(1, 2), add(2, 1))`, `same(add(2, 3), add(3, 2))` and so on).

## The power of shrinking

Another power of writing proofs like this is that since we _declared_ the kind of input, when it finds a failing scenario it will _shrink_ the values in order to find the smallest values that make the scenario fail.

For integers this means shrinking towards `0`, for strings removing characters from both ends, same for lists, etc...

The result is that BlackBox helps you pinpoint the root of the problem by eliminating automatically possibilities.

You can visualize this operation because it will print an `S` each time it's able to shrink the problem.

![](shrinking.png)
