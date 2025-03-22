---
hide:
    - navigation
---

# Configuring the test runner

## Changing the number of scenarii for each proof

By default BlackBox will generate `100` scenarii per proof. You may want to increase this number if you want it to be quicker to find a failing scenario. Or you may want to decrease it if you write functional tests as it would take too much time.

To do so:

```php
use Innmind\BlackBox\{
    Application,
    Runner\Load,
};

Application::new([])
    ->scenariiPerProof(1_000)
    ->tryToProve(Load::everythingIn('proofs/'))
    ->exit();
```

## Disabling the shrinking

The shrinking is the process by which BlackBox will try to find the smallest input that make a scenario fail. You may want to disable it in some cases like writing functional tests as it may take too much time.

To do so:

```php
use Innmind\BlackBox\{
    Application,
    Runner\Load,
};

Application::new([])
    ->disableShrinking()
    ->tryToProve(Load::everythingIn('proofs/'))
    ->exit();
```

## Code coverage

You can record the code covered by your proofs and dump the report to a file like this:

```php
use Innmind\BlackBox\{
    Application,
    Runner\Load,
    Runner\CodeCoverage,
};

Application::new([])
    ->codeCoverage(
        CodeCoverage::of('src/')
            ->dumpTo('coverage.clover'),
    )
    ->tryToProve(Load::everythingIn('proofs/'))
    ->exit();
```

## Changing the way the framework outputs the results

By default the framework outputs any data as soon as possible to keep the usage of memory low but this means rewinding the output to find a failure. If you want to change the output you need to implement the interface `Innmind\BlackBox\Runner\Printer` and declare it liek this:

```php
use Innmind\BlackBox\{
    Application,
    Runner\Load,
};

Application::new([])
    ->usePrinter(new YourPrinter())
    ->tryToProve(Load::everythingIn('proofs/'))
    ->exit();
```

## Disable global functions

By default the framework exposes the `proof`, `test`, `property`, `properties` and `given` global functions but if you don't want them to avoid collisions with your own methods you can use the namespaced functions (available in the `Innmind\BlackBox\Runner` namespace).

```php
use Innmind\BlackBox\{
    Application,
    Runner\Load,
};

Application::new([])
    ->disableGlobalFunctions()
    ->tryToProve(Load::everythingIn('proofs/'))
    ->exit();
```

## Disable GitHub Action output

When it detects it's run inside a GitHub Action the framework groups each proof output to make the output more compact for large suites. It also adds annotations to quickly jump to each failing proof.

You can disable such behaviour like this:

```php hl_lines="4 8"
use Innmind\BlackBox\{
    Application,
    Runner\Load,
    Runner\Printer\Standard,
};

Application::new([])
    ->usePrinter(Standard::new()->disableGitHubOutput())
    ->tryToProve(Load::everythingIn('proofs/'))
    ->exit();
```

## Allow proofs to not make any assertions

By default BlackBox will fail a proof when a scenario did not make any assertion. This is to make sure proof are correctly written and none that make no assertions goes unnoticed.

However if your style of making assertions may not always lead to a proof making one, then you can disable this feature this way:

```php hl_lines="4 8"
use Innmind\BlackBox\{
    Application,
};

Application::new([])
    ->allowProofsToNotMakeAnyAssertions()
    ->tryToProve(static function() {
        yield proof(
            'Some proof',
            given(Set::of('some input')),
            static function($assert, $input) {
                try {
                    doSomething($input);
                    $assert->fail('It should throw');
                } catch (\Exception $e) {
                    // expected behaviour
                }
            },
        );
    })
    ->exit();
```
