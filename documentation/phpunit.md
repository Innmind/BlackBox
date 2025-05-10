---
hide:
    - navigation
---

# PHPUnit

If you don't yet feel confident completely switching to a different test runner, BlackBox can also be used with PHPUnit.

## As a data provider

```php title="MyTestCase.php"
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};
use PHPUnit\Framework\{
    TestCase,
    Attributes\DataProvider,
};

final class MyTestCase extends TestCase
{
    use BlackBox;

    #[DataProvider('values')]
    public function testAddIsCommutative(int $a, int $b)
    {
        $this->assertSame(
            add($a, $b),
            add($b, $a),
        );
    }

    public static function values(): iterable
    {
        return self::forAll(
            Set::integers(),
            Set::integers(),
        )->asDataProvider();
    }
}
```

This will generate `100` scenarii for the test.

!!! warning ""
    By using BlackBox as a data provider you enter into some limitations:

    - you won't benefit from the [shrinking mechanism](preface/terminology.md#shrinking)
    - you may run out of memory (since PHPUnit keep in memory all scenarii data)

## Like BlackBox

The previous example becomes:

```php title="MyTestCase.php"
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};
use PHPUnit\Framework\TestCase;

final class MyTestCase extends TestCase
{
    use BlackBox;

    public function testAddIsCommutative(int $a, int $b)
    {
        $this
            ->forAll(
                Set::integers(),
                Set::integers(),
            )
            ->then(function(int $a, int $b) {
                $this->assertSame(
                    add($a, $b),
                    add($b, $a),
                );
            });
    }
}
```

!!! note ""
    Here you need to use the PHPUnit methods to write your assertions. BlackBox only deals with generating and shrinking data.

Like this BlackBox is able to shrink a failing scenario. But to see the generated input values you need to add an extension.

```xml title="phpunit.xml.dist" hl_lines="7-10"
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    colors="true"
    bootstrap="vendor/autoload.php"
    xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.1/phpunit.xsd">
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

If you want to take a look at a migration you can look at [BlackBox's own PHPUnit tests](https://github.com/Innmind/BlackBox/tree/master/tests/) that are now run via BlackBox itself.

!!! success ""
    Running BlackBox's PHPUnit tests via BlackBox increase execution speed by 35% (from ~7.1s down to ~4.6s) on a MackBook Pro M1 Max.

### Display all scenarii

If you've declared your [proofs inside a PHPUnit test method](#like-blackbox), when run directly via BlackBox it will show up as a single scenario. On top of that you can't see the [shrinking mechanism](preface/terminology.md#shrinking).

You can better integrate these proofs inside BlackBox via these changes:

```php title="MyTestCase.php" hl_lines="11 13 18"
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};
use PHPUnit\Framework\TestCase;

final class MyTestCase extends TestCase
{
    use BlackBox;

    public function testAddIsCommutative(int $a, int $b): BlackBox\Proof
    {
        return $this
            ->forAll(
                Set::integers(),
                Set::integers(),
            )
            ->prove(function(int $a, int $b) {
                $this->assertSame(
                    add($a, $b),
                    add($b, $a),
                );
            });
    }
}
```

Instead of calling `#!php $this->forAll()->then()` you call `#!php $this->forAll()->prove()` and return the object.

Now each scenario will be correctly displayed and the number of scenarii configured in `Innmind\BlackBox\Application` is correctly respected (no need to use the `BLACKBOX_SET_SIZE` environment variable anymore).

??? note
    Note that the calls to `#!php ->take()` and `#!php ->disableShrinking()` won't do anything in this context as it uses the global configuration.

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
