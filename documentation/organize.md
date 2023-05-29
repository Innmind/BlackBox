# Organize your proofs

BlackBox use generators to feed the framework the proofs to verify. This approach allows for the use of an anonymous function when you have very few [proofs](proof.md).

But in a real application you'll probably have hundreds or even thousands of them and the anonymous function is clearly no longer a good fit. In such case you should create multiple files that return anonymous functions containing your proofs.

```php
# proofs/some-file.php
return static function() {
    yield proof(/* args */);
    yield proof(/* args */);
    yield proof(/* args */);
    // etc...
};
```

And to load them:

```php
use Innmind\BlackBox\{
    Application,
    Runner\Load,
};

Application::new([])
    ->tryToProve(Load::everythingIn('proofs/'))
    ->exit();
```

This will recursively load all files in the `proofs/` folder and sub folders.
