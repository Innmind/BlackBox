# Time

=== "In less than x milliseconds"
    ```php
    static function(Assert $assert) {
        $assert
            ->time(static function() {
                // execute your code here
            })
            ->inLessThan()
            ->milliseconds($number, 'Optional error message');
    }
    ```

=== "In less than x seconds"
    ```php
    static function(Assert $assert) {
        $assert
            ->time(static function() {
                // execute your code here
            })
            ->inLessThan()
            ->seconds($number, 'Optional error message');
    }
    ```

=== "In more than x milliseconds"
    ```php
    static function(Assert $assert) {
        $assert
            ->time(static function() {
                // execute your code here
            })
            ->inMoreThan()
            ->milliseconds($number, 'Optional error message');
    }
    ```

=== "In more than x seconds"
    ```php
    static function(Assert $assert) {
        $assert
            ->time(static function() {
                // execute your code here
            })
            ->inMoreThan()
            ->seconds($number, 'Optional error message');
    }
    ```
