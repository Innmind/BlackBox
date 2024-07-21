# Exceptions

=== "Must throw"
    ```php
    static function(Assert $assert) {
        $assert->throws(
            static function() {
                // execute some code that must throw an exception
            },
            $optionalExceptionClass,
            'Optional error message',
        );
    }
    ```

    If the code throws an exception but it's not an instance of `$optionalExceptionClass` then the assertion fails.

=== "Must not throw"
    ```php
    static function(Assert $assert) {
        $assert
            ->not()
            ->throws(
                static function() {
                    // execute some code that must not throw an exception
                },
                'Optional error message',
            );
    }
    ```
