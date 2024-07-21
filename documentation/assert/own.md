# Write your own

## Stateless

A simple example would be to assert a string is a valid uuid.

```php title="IsUuid.php"
final class IsUuid
{
    public function __construct(
        private mixed $value
    ) {}

    public function __invoke(Assert $assert): void
    {
        $assert
            ->string($this->value)
            ->matches(
                '/^[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$/',
                'Value is not a uuid',
            );
    }
}
```

You can now use this assertion everywhere like this:

```php
static function(Assert $assert) {
    $assert->matches(new IsUuid($someValue));
}
```

## Stateful

In some cases you want to carry a state when asserting values. This is usually the case when testing an API.

The approach is to wrap the `Assert` object in an object of your own.

Let's say you want to make HTTP requests and sometimes you want to impersonate them.

```php title="Requests.php"
use Innmind\BlackBox\Runner\Assert;

final class Requests
{
    public function __construct(
        private Assert $assert,
        private ?string $bearer = null,
    ) {}

    public function responds(string $url): self
    {
        $response = somehowDoAnHTTPCallTo($url, $this->bearer);

        $this->assert->same(200, $response->code());

        return $this;
    }

    /**
     * @param callable(self): void $action
     */
    public function impersonate(
        string $bearer,
        callable $action,
    ): self {
        $action(new self($this->assert, $bearer));

        return $this;
    }
}
```

Now you can do:

```php linenums="1"
static function(Assert $assert) {
    $requests = new Requests($assert);

    $requests
        ->responds('https://github.com')
        ->impersonate(
            'some Authorization bearer',
            static fn(Requests $requests) => $requests->responds(
                'https://github.com/settings/profile',
            ),
        )
        ->responds('https://google.com');
}
```

Calls to `responds` on line `5` and `12` do not use a bearer while the one on line `8` does.
