# BlackBox

| `develop` |
|-----------|
| [![codecov](https://codecov.io/gh/Innmind/BlackBox/branch/develop/graph/badge.svg)](https://codecov.io/gh/Innmind/BlackBox) |
| [![Build Status](https://travis-ci.org/Innmind/BlackBox.svg?branch=develop)](https://travis-ci.org/Innmind/BlackBox) |

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
    public static function some(): Set
    {
        return new Set\Composite(
            function($firstName, $lastName): User {
                return new User($firstName, $lastName);
            },
            new Set\Strings, // firstNames
            new Set\Strings // lastNames
        );
    }
}

class UserTest extends \PHPUnit\Framework\TestCase
{
    use BlackBox;

    public function testUserGreetsWithHisFirstName()
    {
        $this
            ->forAll(UserSet::some())
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

The same example with `eris` would you like this:

```php
class UserTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;

    public function testUserGreetsWithHisFirstName()
    {
        $this
            ->forAll(
                \Eris\Generator\string(),
                \Eris\Generator\string()
            )
            ->then(function($firstName, $lastName) {
                $user = new User($firstName, $lastName);

                $this->assertSame(
                    "Hi, I'm $firstName",
                    $user->greet()
                );
            });
    }
}
```

There's also a difference in how this library filters sets compared to `eris`, here the number of elements taken from the sets is done after the filtering meaning there's always the expected amount of elements as expected whereas `eris` applies the filtering after taking the number of elements meaning that sometimes there may not be any elements in the set to test against.
