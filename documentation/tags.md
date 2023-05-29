# Using tags

Tags are a way to organize your proofs and run only a subset of them. BlackBox uses enums as tags to make sure you don't make a typo on a proof that may result in it never being verified.

By default BlackBox comes with the enum `Innmind\BlackBox\Tag`.

And you can use it like this:

```php
# blackbox.php
use Innmind\BlackBox\{
    Application,
    Tag,
};

Application::new($argv)
    ->tryToProve(static function() {
        yield proof(
            'X works on linux',
            // ...
        )->tag(Tag::positive, Tag::linux);
        yield proof(
            'X works on macOS',
            // ...
        )->tag(Tag::positive, Tag::macOS);
        yield proof(
            'X fail a certain way on linux',
            // ...
        )->tag(Tag::negative, Tag::linux);
        yield proof(
            'X fail a certain way on macOS',
            // ...
        )->tag(Tag::negative, Tag::macOS);
        yield proof(
            "Proof I'm working on",
            //...
        )->tag(Tag::wip);
    })
    ->exit();
```

The tags to run are specified by the `array` provided to `Application::new()`. In this case we use `$argv` which means you can do `php blackbox.php wip`, `php blackox.php linux`, `php blackox.php positive macOS`, and so on...

## Using you own enum

If the default tags are not enough you can use your own enum, but you need to specify a callable to transform strings back to your enum.

```php
use Innmind\BlackBox\Application;

Application::new($args)
    ->parseTagWith(static fn(string $value): ?YourEnum => match ($value) {
        'foo' => YourEnum::foo,
        default => null,
    })
    ->tryToProve(/* proofs */)
    ->exit();
```
