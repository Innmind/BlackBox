# Memory

In order for these assertions to work properly you need to:

- add `declare(ticks = 1);` at the top of file where you call the assertion
- the callable cannot use the anonymous function short notation

??? example
    ```php title="blackbox.php" hl_lines="3"
    <?php
    declare(strict_types = 1);
    declare(ticks = 1);

    require 'path/to/vendor/autoload.php';

    use Innmind\BlackBox\{
        Application,
        Runner\Assert,
    };

    Application::new([])
        ->tryToProve(static function(): \Generator {
            yield test(
                'Some memory test',
                static function(Assert $assert) {
                    $assert
                        ->memory(static function() {
                            // your code here
                        })
                        ->inLessThan()
                        ->megaBytes(42);
                },
            );
        })
        ->exit();
    ```

=== "In less than x Bytes"
    ```php
    static function(Assert $assert) {
        $assert
            ->memory(static function() {
                // execute your code here
            })
            ->inLessThan()
            ->bytes($number, 'Optional error message');
    }
    ```

=== "In less than x KiloBytes"
    ```php
    static function(Assert $assert) {
        $assert
            ->memory(static function() {
                // execute your code here
            })
            ->inLessThan()
            ->kiloBytes($number, 'Optional error message');
    }
    ```

=== "In less than x MegaBytes"
    ```php
    static function(Assert $assert) {
        $assert
            ->memory(static function() {
                // execute your code here
            })
            ->inLessThan()
            ->megaBytes($number, 'Optional error message');
    }
    ```

=== "In more than x Bytes"
    ```php
    static function(Assert $assert) {
        $assert
            ->memory(static function() {
                // execute your code here
            })
            ->inMoreThan()
            ->bytes($number, 'Optional error message');
    }
    ```

=== "In more than x KiloBytes"
    ```php
    static function(Assert $assert) {
        $assert
            ->memory(static function() {
                // execute your code here
            })
            ->inMoreThan()
            ->kiloBytes($number, 'Optional error message');
    }
    ```

=== "In more than x MegaBytes"
    ```php
    static function(Assert $assert) {
        $assert
            ->memory(static function() {
                // execute your code here
            })
            ->inMoreThan()
            ->megaBytes($number, 'Optional error message');
    }
    ```
