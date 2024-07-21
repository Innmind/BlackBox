# Assert

The `Innmind\BlackBox\Runner\Assert` object is the only way to make assertions in your tests/proofs/properties.

This object is always passed as an argument where you should apply assertions. If you don't have access to it, then you're doing something wrong.

The following chapters will guide you through all the assertions you can use.

The base assertions are:

=== "Same"
    ```php
    static function(Assert $assert) {
        $assert->same($expected, $someValue, 'Optional error message');
        // or
        $assert
            ->expected($expected)
            ->same($someValue, 'Optional error message');
    }
    ```

    Think `===`.

=== "Not same"
    ```php
    static function(Assert $assert) {
        $assert
            ->expected($expected)
            ->not()
            ->same($someValue, 'Optional error message');
    }
    ```

    Think `!==`.

=== "Equals"
    ```php
    static function(Assert $assert) {
        $assert
            ->expected($expected)
            ->equals($someValue, 'Optional error message');
    }
    ```

    Think `==`.

=== "Not equals"
    ```php
    static function(Assert $assert) {
        $assert
            ->expected($expected)
            ->not()
            ->equals($someValue, 'Optional error message');
    }
    ```

    Think `!=`.

=== "Force a failure"
    ```php
    static function(Assert $assert) {
        $assert->fail('Error message');
    }
    ```

    This can be useful when dealing with [exceptions](exceptions.md).
