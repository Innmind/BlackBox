# Writing your first test with BlackBox

If you landed here you're probably familiar with writing tests via tools (such as PHPUnit, Pest, etc...). Even though BlackBox is oriented for writing proofs you can also write _normal_ tests (maybe it unit or functional) with it.

Let's say you have a `add` function you want to test, you can do it via:

```php title="blackbox.php"
use Innmind\BlackBox\{
    Application,
    Runner\Assert,
};

Application::new([])
    ->tryToProve(static function() {
        yield test(
            'add',
            static fn(Assert $assert) => $assert
                ->expected(3)
                ->same(add(1, 2)),
        );
    })
    ->exit();
```

To run the tests you have execute this file via `php blackbox.php`.

If your test is successful the command will return a `0` exit code, and `1` on failure.

In the BlackBox vocabulary a test is a _scenario_. This means that you manually specify the values you want to test your code with. Writing tests is useful when you want to verify something that doesn't depend on some input (ie a class is an instance of some interface) or when you found a regression and you want to test this specific scenario.
