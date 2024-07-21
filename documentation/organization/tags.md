# Tags

Sometimes you may want to only run a subset of your tests/proofs/properties. The best case is when you're creating new ones. You'll probably run them many times until you get them right.

You can do this with tags. Tags are defined by any `enum`. By default you have access to:

```php
namespace Innmind\BlackBox;

enum Tag
{
    case windows;
    case linux;
    case macOS;
    case positive;
    case negative;
    case wip;
    case ci;
    case local;
}
```

To use them:

```php title="blackbox.php"
use Innmind\BlackBox\{
    Application,
    Tag,
    Runner\Assert,
};

Application::new($argv) #(1)
    ->tryToProve(static function(): \Generator {
        yield test(
            'WIP test',
            static function(Assert $assert) {
                // your code here
            },
        )->tag(Tag::wip);

        yield test(
            'Another test',
            static function(Assert $assert) {
                // your code here
            },
        );
    })
    ->exit();
```

1. The array passed to `new` is the list of tags to filter on. By using `$argv` it allows to specify the tags from the command line.

If you run `php blackbox.php wip` it will run `WIP test` but not `Another test`.

You can define multiple tags on each test/proofs/properties.

## Add your own tags

The builtin tags may not be enough for your project. You may want to tag them by business logic for example.

This takes 2 steps.

First create your enum:

```php title="Business.php"
enum Business
{
    case billing;
    case shipping;
    case warehouse;

    public static function of(string $string): ?self
    {
        return match ($string) {
            'billing' => self::billing,
            'shipping' => self::shipping,
            'warehouse' => self::warehouse,
            default => null,
        };
    }
}
```

Then make BlackBox aware of it:

```php title="blackbox.php"
use Innmind\BlackBox\{
    Application,
    Runner\Assert,
};

Application::new($argv)
    ->parseTagWith(Business::of(...))
    ->tryToProve(static function(): \Generator {
        yield test(
            'Shipping test',
            static function(Assert $assert) {
                // your code here
            },
        )->tag(Business::shipping);

        yield test(
            'Billing test',
            static function(Assert $assert) {
                // your code here
            },
        )->tag(Business::billing);
    })
    ->exit();
```
