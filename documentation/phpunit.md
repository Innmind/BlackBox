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
