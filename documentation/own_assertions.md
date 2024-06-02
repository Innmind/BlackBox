# Adding assertions

BlackBox comes with a fixed set of assertions provided by the `Assert` class. But sometime you may want to reuse an assertion (or set of assertions) thoughout your proofs. You can do so with the `Assert::matches()` method.

Let's say you want to validate a Uuid, you could create a static method on a class like this:

```php
use Innmind\BlackBox\Runner\Assert;

final class Uuid
{
    /**
     * @return callable(Assert): void
     */
    public static function of(mixed $value): callable
    {
        return static fn(Assert $assert) => $assert
            ->string($value)
            ->matches('~^[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$~');
    }
}
```

And in your proof you would use it like this:

```php
use Innmind\BlackBox\{
    Set,
    Runner\Assert,
};

yield proof(
    'Name of your proof',
    given(Set\Type::any()),
    static fn(Assert $assert, $value) => $assert->matches(Uuid::of($value)),
);
```

!!! note ""
    This example proof will fail because generated values are not uuids.
