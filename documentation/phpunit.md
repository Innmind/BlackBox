# Compatibility with PHPUnit

If you don't feel confident yet completely switching to a different test runner, BlackBox can also be used with PHPUnit. This is feasable in 2 steps.

The first one is to use a `trait` in your `TestCase`.

```php
final class MyTestCase extends TestCase
{
    use BlackBox;

    public function testAddIsCommutative()
    {
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::any(),
            )
            ->then(function(int $a, int $b) {
                // use the PHPUnit assertions as usual
                $this->assertSame(
                    add($a, $b),
                    add($b, $a),
                );
            });
    }
}
```

And to be able to see the data generated for a failing scenario you need to add an extension to your `phpunit.xml.dist`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" colors="true" bootstrap="vendor/autoload.php" xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.1/phpunit.xsd">
  <extensions>
    <bootstrap class="Innmind\BlackBox\PHPUnit\Extension">
    </bootstrap>
  </extensions>
  <testsuites>
    <testsuite name="Test suite">
      <directory>./tests</directory>
    </testsuite>
  </testsuites>
  <source>
    <include>
      <directory>.</directory>
    </include>
  </source>
</phpunit>
```

## Configuration

You can change the number of scenarii per test for the whole test suite via the `BLACKBOX_SET_SIZE` environment variable (default to `100`).

You can also disable shrinking for the whole test suite via the `BLACKBOX_DISABLE_SHRINKING` environment variable with the value `'1'`.

## Data provider

You can also use BlackBox as a data provider meaning that you can replace your hard coded cases by generated ones by BlackBox. The example above would look like this with a data provider:

```php
use PHPUnit\Framework\Attributes\DataProvider;

final class MyTestCase extends TestCase
{
    use BlackBox;

    #[DataProvider('ints')]
    public function testAddIsCommutative(int $a, int $b)
    {
        $this->assertSame(
            add($a, $b),
            add($b, $a),
        );
    }

    public static function ints(): iterable
    {
        return self::forAll(
            Set\Integers::any(),
            Set\Integers::any(),
        )->asDataProvider();
    }
}
```

!!! warning ""
    By using BlackBox as a data provider you enter into some limitations:

    - you won't benefit from the [shrinking mechanism](proof.md#the-power-of-shrinking)
    - you won't benefit from the output of the generated data that make you test fail
    - you may run out of memory (since PHPUnit keep in memory all scenarii data)

## Running your tests via BlackBox

If you wish to migrate to BlackBox but don't want to rewrite all your existing tests you can run them directly via BlackBox.

The first step is to prefix the `PHPUnit\Framework\TestCase` class with `Innmind\BlackBox\`.

The second step is to load the tests like this:

```php
use Innmind\BlackBox\{
    Application,
    PHPUnit\Load,
};

Application::new()
    ->tryToProve(function() {
        yield from Load::testsAt('path/to/your/tests');
    })
    ->exit();
```

If you want to take a look at a migration you can look at [BlackBox's own PHPUnit tests](../tests/) that are now run via BlackBox itself.

!!! note ""
    Running BlackBox's PHPUnit tests via BlackBox increase execution speed by 35% (from ~7.1s down to ~4.6s) on a MackBook Pro M1 Max.

### Feature coverage

PHPUnit is a very large testing framework with lots of features. BlackBox doesn't support all its features when running your tests.

Supported features:

- test `setUp()`/`tearDown()`
- assertions that have a correspondance in BlackBox
- data providers declared with an attribute
- groups declared with an attribute (the name must have a correspondance in `Innmind\BlackBox\Tag`)

Some important features that are not supported:

- mocks
- classes `setUpBeforeClass()`/`tearDownAfterClass()`
- assertions that don't have a correspondance in BlackBox (such as files assertions)
