# Organization

So far all tests/proofs/properties have been declared in the `blackbox.php` file. This is fine for examples. But in a real project with hundreds of them this is not manageable.

Since it uses a `Generator` you can easily split them into multiple files:

=== "BlackBox"
    ```php title="blackbox.php"
    use Innmind\BlackBox\{
        Application,
        Prove,
    };

    Application::new([])
        ->tryToProve(static function(Prove $prove): \Generator {
            yield from (require 'proofs/file1.php')($prove);
            yield from (require 'proofs/file2.php')($prove);
            yield from (require 'proofs/etc.php')($prove);
        })
        ->exit();
    ```

=== "File1"
    ```php title="proofs/file1.php"
    use Innmind\BlackBox\{
        Runner\Assert,
        Set,
        Prove,
    };

    return static function(Prove $prove): \Generator {
        yield $prove
            ->proof('Some proof')
            ->given(Set::integers()),
            ->test(static function(Assert $assert, int $value) {
                // your code here
            });
    };
    ```

=== "File2"
    ```php title="proofs/file2.php"
    use Innmind\BlackBox\{
        Runner\Assert,
        Set,
        Prove,
    };

    return static function(Prove $prove): \Generator {
        yield $prove
            ->proof('Some proof')
            ->given(Set::strings()),
            ->test(static function(Assert $assert, int $value) {
                // your code here
            });
    };
    ```

=== "Etc..."
    ```php title="proofs/etc.php"
    use Innmind\BlackBox\{
        Runner\Assert,
        Set,
        Prove,
    };

    return static function(Prove $prove): \Generator {
        yield $prove->test(
            'Some proof',
            static function(Assert $assert) {
                // your code here
            },
        );
    };
    ```

This way you can enforce the order in which the files are loaded. However it becomes tedious to modify `blackbox.php` each time you add a file in `proofs/`.

Instead you can load all files like this:

```php title="blackbox.php" hl_lines="3 7"
use Innmind\BlackBox\{
    Application,
    Runner\Load,
};

Application::new([])
    ->tryToProve(Load::everythingIn('proofs/'))
    ->exit();
```
