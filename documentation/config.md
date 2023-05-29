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
