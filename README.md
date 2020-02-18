# BlackBox

[![codecov](https://codecov.io/gh/Innmind/BlackBox/branch/develop/graph/badge.svg)](https://codecov.io/gh/Innmind/BlackBox)
[![Build Status](https://github.com/Innmind/BlackBox/workflows/CI/badge.svg)](https://github.com/Innmind/BlackBox/actions?query=workflow%3ACI)

Contains an ensemble of sets to easily generate data for property based tests.

## Philosophy

When I run tests I need some data to assert the validity of my code, the first approach is to hardcode the test data in the test class itself but it lacks enough variety in order to make sure all (or at least enough) cases are covered. In order to generate data we can use a property based testing library such as [`giorgiosironi/eris`](https://packagist.org/packages/giorgiosironi/eris), but the problem is that for each test you need to redeclare the base sets of data you need test against.

The goal of this library is to help build higher order sets to facilitate the understanding of tests.

**Note**: the library only generates primitives types, any user defined type set must be declared in its dedicated package.

## Installation

```sh
composer require innmind/black-box
```

## Usage

```php
use Innmind\BlackBox\{
    Set,
    PHPUnit\BlackBox,
};

final class User
{
    private $firstName;
    private $lastName;

    public function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function greet(): string
    {
        return "Hi, I'm {$this->firstName}";
    }
}

class UserSet
{
    public static function any(): Set
    {
        return Set\Composite::immutable(
            function($firstName, $lastName): User {
                return new User($firstName, $lastName);
            },
            Set\Strings::any(), // firstNames
            Set\Strings::any() // lastNames
        );
    }
}

class UserTest extends \PHPUnit\Framework\TestCase
{
    use BlackBox;

    public function testUserGreetsWithHisFirstName()
    {
        $this
            ->forAll(UserSet::any())
            ->then(function(User $user) {
                $this->assertSame(
                    "Hi, I'm {$user->firstName()}",
                    $user->greet()
                );
            });
    }
}
```

This really simple example show how the test class is focused on the behaviour and not about the construction of the test data.

**Note**: here the `User` class is not mutable, but in your application it's likely that such class (meaning an entity) would be mutable, in such case you MUST use `Composite::mutable()` otherwise a mutated object would bleed between the iterations of the test.

By default the library supports the shrinking of data to help you find the smallest possible set of values that makes your test fail. To help you ease the debugging of the code you can use the printer class `Innmind\BlackBox\PHPUnit\ResultPrinterV8` that will print the set of generated data that made your test fail.

![](printer.png)

**Important**: shrinking use recursion to find the smallest value thus generating deep call stacks, so you may need to disable the xdebug `Maximum function nesting level` option.
