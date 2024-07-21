# Objects

=== "Is an object"
    ```php
    static function(Assert $assert) {
        $assert->object($someValue);
    }
    ```

=== "Is an instance of"
    ```php
    static function(Assert $assert) {
        $assert
            ->object($someValue)
            ->instance($someClass, 'Optional error message');
    }
    ```

=== "Isn't an instance of"
    ```php
    static function(Assert $assert) {
        $assert
            ->object($someValue)
            ->not()
            ->instance($someClass, 'Optional error message');
    }
    ```
