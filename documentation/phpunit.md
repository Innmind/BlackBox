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

**Warning**: By using BlackBox as a data provider you enter into some limitations:
- you won't benefit from the [shrinking mechanism](proof.md#the-power-of-shrinking)
- you won't benefit from the output of the generated data that make you test fail
- you may run out of memory (since PHPUnit keep in memory all scenarii data)
