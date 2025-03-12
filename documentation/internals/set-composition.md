# `Set` composition

It's possible to compose `Set`s via the `flatMap` method. This allows to randomly configure other `Set`s.

One of the main features of `Set`s is that they can shrink the generated values. This helps find the minimum values that make a test fail.

But in the case of the use of `Set::flatMap()` the value generated to configure another `Set` escape the control of the parent `Set`.

In order for this value to still be shrunk, BlackBox needs to track every use of it. To achieve this the value is wrapped in a `Seed` monad. This monad records every transformation (the callables passed to `map` and `flatMap`) and apply them when unwrapping the value. This way we can shrink the initial value and then re-apply the transformations.

If a user calls `Seed::unwrap()` to directly manipulate the value then BlackBox can no longer shrink this value.

While this design allows to maintain every feature across all possible compositions it introduces a complexity [problem in the shrinking design](shrinking.md#seeded-values).
