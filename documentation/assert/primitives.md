# Primitives

## Null

=== "Is null"
    ```php
    static function(Assert $assert) {
        $assert->null($someValue, 'Optional error message');
    }
    ```

=== "Is not null"
    ```php
    static function(Assert $assert) {
        $assert
            ->not()
            ->null($someValue, 'Optional error message');
    }
    ```

## Booleans

=== "True or False"
    ```php
    static function(Assert $assert) {
        $assert->bool($someValue, 'Optional error message');
    }
    ```

=== "True"
    ```php
    static function(Assert $assert) {
        $assert->true($someValue, 'Optional error message');
    }
    ```

=== "False"
    ```php
    static function(Assert $assert) {
        $assert->false($someValue, 'Optional error message');
    }
    ```

=== "Neither True nor False"
    ```php
    static function(Assert $assert) {
        $assert->not()->bool($someValue, 'Optional error message');
    }
    ```

=== "Not True"
    ```php
    static function(Assert $assert) {
        $assert->not()->true($someValue, 'Optional error message');
    }
    ```

=== "Not False"
    ```php
    static function(Assert $assert) {
        $assert->not()->false($someValue, 'Optional error message');
    }
    ```

## Resource

```php
static function(Assert $assert) {
    $assert->resource($someValue);
}
```

`resource`s are values return by `fopen` functions and such.

## Numbers

=== "Any number"
    ```php
    static function(Assert $assert) {
        $assert->number($someValue);
    }
    ```

=== "Is an integer"
    ```php
    static function(Assert $assert) {
        $assert
            ->number($someValue)
            ->int('Optional error message');
    }
    ```

=== "Is a float"
    ```php
    static function(Assert $assert) {
        $assert
            ->number($someValue)
            ->float('Optional error message');
    }
    ```

=== "Greater than"
    ```php
    static function(Assert $assert) {
        $assert
            ->number($someValue)
            ->greaterThan($someIntOrFloat, 'Optional error message');
    }
    ```

=== "Greater than or equal"
    ```php
    static function(Assert $assert) {
        $assert
            ->number($someValue)
            ->greaterThanOrEqual($someIntOrFloat, 'Optional error message');
    }
    ```

=== "Less than"
    ```php
    static function(Assert $assert) {
        $assert
            ->number($someValue)
            ->lessThan($someIntOrFloat, 'Optional error message');
    }
    ```

=== "Less than or equal"
    ```php
    static function(Assert $assert) {
        $assert
            ->number($someValue)
            ->lessThanOrEqual($someIntOrFloat, 'Optional error message');
    }
    ```

## Strings

=== "Any string"
    ```php
    static function(Assert $assert) {
        $assert->string($someValue);
    }
    ```

=== "Is empty"
    ```php
    static function(Assert $assert) {
        $assert
            ->string($someValue)
            ->empty('Optional error message');
    }
    ```

=== "Contains a value"
    ```php
    static function(Assert $assert) {
        $assert
            ->string($haystack)
            ->contains($needle, 'Optional error message');
    }
    ```

=== "Matches a regex"
    ```php
    static function(Assert $assert) {
        $assert
            ->string($someValue)
            ->matches($regex, 'Optional error message');
    }
    ```

=== "Starts with"
    ```php
    static function(Assert $assert) {
        $assert
            ->string($someValue)
            ->startsWith($prefix, 'Optional error message');
    }
    ```

=== "Ends with"
    ```php
    static function(Assert $assert) {
        $assert
            ->string($someValue)
            ->endsWith($suffix, 'Optional error message');
    }
    ```

You can add a call to `->not()` after `->string()` to inverse the following assertion.

You can chain multiple assertions on the same string like this:

```php
static function(Assert $assert) {
    $assert
        ->string($someValue)
        ->startsWith($prefix, 'Optional error message')
        ->endsWith($suffix, 'Optional error message');
}
```

## Arrays

=== "Is an array"
    ```php
    static function(Assert $assert) {
        $assert->array($someValue);
    }
    ```

=== "Has a key"
    ```php
    static function(Assert $assert) {
        $assert
            ->array($someValue)
            ->hasKey($intOrString, 'Optional error message');
    }
    ```

=== "Doesn't have a key"
    ```php
    static function(Assert $assert) {
        $assert
            ->array($someValue)
            ->not()
            ->hasKey($intOrString, 'Optional error message');
    }
    ```

=== "Contains a value"
    ```php
    static function(Assert $assert) {
        $assert
            ->array($array)
            ->contains($value, 'Optional error message');
    }
    ```

=== "Doesn't contain a value"
    ```php
    static function(Assert $assert) {
        $assert
            ->array($array)
            ->not()
            ->contains($value, 'Optional error message');
    }
    ```

=== "Count"
    ```php
    static function(Assert $assert) {
        $assert->count($count, $array, 'Optional error message');
    }
    ```

=== "Doesn't have count"
    ```php
    static function(Assert $assert) {
        $assert
            ->not()
            ->count($count, $array, 'Optional error message');
    }
    ```
