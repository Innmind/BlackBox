# BlackBox

## Example

```php
$test = test('adding 2 integers together procude the expected result',
    given(
        any('a', Set\integers()),
        any('b', Set\integers())
    ),
    when(static function($given) {
        return add($given->a, $given->b);
    }),
    then(
        Assert\int(),
        Assert\that(static function($result, $given): bool {
            return $result === $given->a + $given->b;
        });
    )
);
```
