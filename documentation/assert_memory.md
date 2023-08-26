# Assert memory usage

You can use BlackBox to make sure use less than a specified amount of memory to make sure your code doesn't have a memory leak. You can do so with the `Assert::memory()` method.

In order for this assertion to work properly you need to:
- add `declare(ticks = 1);` at the top of file where you call the assertion
- the callable cannot use the anonymous function short notation

For example:

```php
<?php
declare(strict_types = 1);
declare(ticks = 1);

return static function() {
    yield test(
        'Your test name',
        static function($assert) {
            $assert
                ->memory(static function() {
                    $yourSystem = new YourSystem();
                    $yourSystem->doStuff();
                })
                ->inLessThan()
                ->megaBytes(1);
        },
    );
};
```
