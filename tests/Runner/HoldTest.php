<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Runner\Hold,
    Runner\TestResult,
    Runner\Arguments,
    Set,
    Set\Value,
    PHPUnit\BlackBox
};
use PHPUnit\Framework\TestCase;

class HoldTest extends TestCase
{
    use BlackBox;

    public function testEither()
    {
        $this
            ->forAll(Set\Elements::of(true, false))
            ->then(function($value) {
                $this->hold(
                    Hold::either(Hold::is(true), Hold::is(false)),
                    $value,
                );
                $this->hold(
                    Hold::either(Hold::is(false), Hold::is(true)),
                    $value,
                );
                $this->doesntHold(
                    Hold::either(Hold::is(null), Hold::is(null)),
                    $value,
                );
            });
    }

    public function testExceptionThrown()
    {
        $this->hold(
            Hold::exceptionThrown(),
            $this->createMock(\Throwable::class),
            'throws',
        );
        $this->doesntHold(
            Hold::exceptionThrown(),
            $this->createMock(\Throwable::class),
        );
    }

    public function testNoExceptionThrown()
    {
        $this->hold(
            Hold::noExceptionThrown(),
            $this->createMock(\Throwable::class),
        );
        $this->doesntHold(
            Hold::noExceptionThrown(),
            $this->createMock(\Throwable::class),
            'throws',
        );
    }

    public function testExceptionCode()
    {
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::any(),
            )
            ->then(function($a, $b) {
                $this->hold(
                    Hold::exceptionCode($a),
                    new \Exception('', $a),
                );
                $this->hold(
                    Hold::exceptionCode($a),
                    new \Exception('', $a),
                    'throws',
                );
                $this->doesntHold(
                    Hold::exceptionCode($a),
                    new \Exception('', $b),
                );
                $this->doesntHold(
                    Hold::exceptionCode($a),
                    new \Exception('', $b),
                    'throws',
                );
            });
    }

    public function testExceptionMessage()
    {
        $this
            ->forAll(
                Set\Strings::any(),
                Set\Strings::any(),
            )
            ->then(function($a, $b) {
                $this->hold(
                    Hold::exceptionMessage($a),
                    new \Exception($a),
                );
                $this->hold(
                    Hold::exceptionMessage($a),
                    new \Exception($a),
                    'throws',
                );
                $this->doesntHold(
                    Hold::exceptionMessage($a),
                    new \Exception($b),
                );
                $this->doesntHold(
                    Hold::exceptionMessage($a),
                    new \Exception($b),
                    'throws',
                );
            });
    }

    public function testInstanceOf()
    {
        $this->hold(
            Hold::instanceOf(\stdClass::class),
            new \stdClass,
        );
        $this->hold(
            Hold::instanceOf(\Exception::class),
            new \Exception,
        );
        $this->hold(
            Hold::instanceOf(\Exception::class),
            new \Exception,
            'throws',
        );
        $this->doesntHold(
            Hold::instanceOf(\Exception::class),
            new \stdClass,
        );
        $this->doesntHold(
            Hold::instanceOf(\stdClass::class),
            new \Exception,
        );
        $this->doesntHold(
            Hold::instanceOf(\stdClass::class),
            new \Exception,
            'throws',
        );
    }

    public function testIsArray()
    {
        $this->hold(
            Hold::isArray(),
            [],
        );
        $this->doesntHold(
            Hold::isArray(),
            null,
        );
    }

    public function testIsBool()
    {
        $this->hold(
            Hold::isBool(),
            true,
        );
        $this->hold(
            Hold::isBool(),
            false,
        );
        $this->doesntHold(
            Hold::isBool(),
            null,
        );
    }

    public function testIsFloat()
    {
        $this
            ->forAll(Set\RealNumbers::any())
            ->then(function($float) {
                $this->hold(
                    Hold::isFloat(),
                    $float,
                );
            });
        $this->doesntHold(
            Hold::isFloat(),
            null,
        );
    }

    public function testIsInt()
    {
        $this
            ->forAll(Set\Integers::any())
            ->then(function($int) {
                $this->hold(
                    Hold::isInt(),
                    $int,
                );
            });
        $this->doesntHold(
            Hold::isInt(),
            null,
        );
    }

    public function testIsNumeric()
    {
        $this
            ->forAll(new Set\Either(
                Set\Integers::any(),
                Set\RealNumbers::any(),
            ))
            ->then(function($number) {
                $this->hold(
                    Hold::isNumeric(),
                    $number,
                );
            });
        $this->doesntHold(
            Hold::isNumeric(),
            null,
        );
    }

    public function testIsObject()
    {
        $this->hold(
            Hold::isObject(),
            new class {
            },
        );
        $this->hold(
            Hold::isObject(),
            new \stdClass,
        );
        $this->hold(
            Hold::isObject(),
            new \Exception,
        );
        $this->doesntHold(
            Hold::isObject(),
            null,
        );
    }

    public function testIsResource()
    {
        $this->hold(
            Hold::isResource(),
            \STDIN,
        );
        $this->hold(
            Hold::isResource(),
            \STDOUT,
        );
        $this->hold(
            Hold::isResource(),
            \STDERR,
        );
        $this->hold(
            Hold::isResource(),
            \tmpfile(),
        );
        $this->doesntHold(
            Hold::isResource(),
            null,
        );
    }

    public function testIsString()
    {
        $this
            ->forAll(
                Set\AnyType::any(),
                Set\Unicode::strings(),
            )
            ->filter(fn($not) => !\is_string($not))
            ->then(function($not, $string) {
                $this->hold(
                    Hold::isString(),
                    $string,
                );
                $this->doesntHold(
                    Hold::isString(),
                    $not,
                );
            });
    }

    public function testIsScalar()
    {
        $this
            ->forAll(new Set\Either(
                Set\Elements::of(true, false),
                Set\Integers::any(),
                Set\RealNumbers::any(),
                Set\Unicode::strings(),
            ))
            ->then(function($value) {
                $this->hold(
                    Hold::isScalar(),
                    $value,
                );
            });
        $this->doesntHold(
            Hold::isScalar(),
            null,
        );
        $this->doesntHold(
            Hold::isScalar(),
            new class {
            },
        );
        $this->doesntHold(
            Hold::isScalar(),
            \tmpfile(),
        );
        $this->doesntHold(
            Hold::isScalar(),
            static fn() => null,
        );
    }

    public function testIsCallable()
    {
        $this->hold(
            Hold::isCallable(),
            'is_callable',
        );
        $this->hold(
            Hold::isCallable(),
            static fn() => null,
        );
        $this->hold(
            Hold::isCallable(),
            [$this, 'testIsCallable'],
        );
        $this->hold(
            Hold::isCallable(),
            \Closure::fromCallable(static fn() => null),
        );
        $this->hold(
            Hold::isCallable(),
            new class {
                public function __invoke()
                {
                }
            },
        );
        $this->doesntHold(
            Hold::isCallable(),
            null,
        );
    }

    public function testIsIterable()
    {
        $this->hold(
            Hold::isIterable(),
            [],
        );
        $this->hold(
            Hold::isIterable(),
            new \ArrayIterator([]),
        );
        $this->doesntHold(
            Hold::isIterable(),
            null,
        );
    }

    public function testNotInstanceOf()
    {
        $object = new class {
        };

        $this->hold(
            Hold::notInstanceOf(\get_class(new class {
            })),
            $object,
        );
        $this->doesntHold(
            Hold::notInstanceOf(\get_class($object)),
            $object,
        );
    }

    public function testIsNotArray()
    {
        $this->hold(
            Hold::isNotArray(),
            null,
        );
        $this->doesntHold(
            Hold::isNotArray(),
            [],
        );
    }

    public function testIsNotBool()
    {
        $this->hold(
            Hold::isNotBool(),
            null,
        );
        $this->doesntHold(
            Hold::isNotBool(),
            true,
        );
        $this->doesntHold(
            Hold::isNotBool(),
            false,
        );
    }

    public function testIsNotFloat()
    {
        $this->hold(
            Hold::isNotFloat(),
            null,
        );
        $this
            ->forAll(Set\RealNumbers::any())
            ->then(function($float) {
                $this->doesntHold(
                    Hold::isNotFloat(),
                    $float,
                );
            });
    }

    public function testIsNotInt()
    {
        $this->hold(
            Hold::isNotInt(),
            null,
        );
        $this
            ->forAll(Set\Integers::any())
            ->then(function($int) {
                $this->doesntHold(
                    Hold::isNotInt(),
                    $int,
                );
            });
    }

    public function testIsNotNumeric()
    {
        $this->hold(
            Hold::isNotNumeric(),
            null,
        );
        $this
            ->forAll(new Set\Either(
                Set\Integers::any(),
                Set\RealNumbers::any(),
            ))
            ->then(function($number) {
                $this->doesntHold(
                    Hold::isNotNumeric(),
                    $number,
                );
            });
    }

    public function testIsNotObject()
    {
        $this->hold(
            Hold::isNotObject(),
            null,
        );
        $this->doesntHold(
            Hold::isNotObject(),
            new class {
            },
        );
        $this->doesntHold(
            Hold::isNotObject(),
            new \Exception,
            'throws',
        );
    }

    public function testIsNotResource()
    {
        $this->hold(
            Hold::isNotResource(),
            null,
        );
        $this->doesntHold(
            Hold::isNotResource(),
            \tmpfile(),
        );
    }

    public function testIsNotString()
    {
        $this->hold(
            Hold::isNotString(),
            null,
        );
        $this
            ->forAll(Set\Unicode::strings())
            ->then(function($string) {
                $this->doesntHold(
                    Hold::isNotString(),
                    $string,
                );
            });
    }

    public function testIsNotScalar()
    {
        $this->hold(
            Hold::isNotScalar(),
            null,
        );
        $this->hold(
            Hold::isNotScalar(),
            new class {
            },
        );
        $this->hold(
            Hold::isNotScalar(),
            \tmpfile(),
        );
        $this->hold(
            Hold::isNotScalar(),
            static fn() => null,
        );
        $this
            ->forAll(new Set\Either(
                Set\Elements::of(true, false),
                Set\Integers::any(),
                Set\RealNumbers::any(),
                Set\Unicode::strings(),
            ))
            ->then(function($value) {
                $this->doesntHold(
                    Hold::isNotScalar(),
                    $value,
                );
            });
    }

    public function isNotCallable()
    {
        $this->hold(
            Hold::isNotCallable(),
            null,
        );
        $this->doesntHold(
            Hold::isNotCallable(),
            'is_callable',
        );
        $this->doesntHold(
            Hold::isNotCallable(),
            static fn() => null,
        );
        $this->doesntHold(
            Hold::isNotCallable(),
            [$this, 'testIsCallable'],
        );
        $this->doesntHold(
            Hold::isNotCallable(),
            \Closure::fromCallable(static fn() => null),
        );
        $this->doesntHold(
            Hold::isNotCallable(),
            new class {
                public function __invoke()
                {
                }
            },
        );
    }

    public function isNotIterable()
    {
        $this->hold(
            Hold::isNotIterable(),
            null,
        );
        $this->doesntHold(
            Hold::isNotIterable(),
            [],
        );
        $this->doesntHold(
            Hold::isNotIterable(),
            new \ArrayIterator([]),
        );
    }

    public function testArrayHasKey()
    {
        $this->hold(
            Hold::arrayHasKey('foo'),
            ['foo' => 'bar'],
        );
        $this->doesntHold(
            Hold::arrayHasKey('foo'),
            [],
        );
    }

    public function testArrayNotHasKey()
    {
        $this->hold(
            Hold::arrayNotHasKey('foo'),
            [],
        );
        $this->doesntHold(
            Hold::arrayNotHasKey('foo'),
            ['foo' => 'bar'],
        );
    }

    public function testInArray()
    {
        $this->hold(
            Hold::inArray('foo'),
            ['foo'],
        );
        $this->doesntHold(
            Hold::inArray('foo'),
            [],
        );
    }

    public function testNotInArray()
    {
        $this->hold(
            Hold::notInArray('foo'),
            [],
        );
        $this->doesntHold(
            Hold::notInArray('foo'),
            ['foo'],
        );
    }

    public function testCount()
    {
        $this
            ->forAll(Set\Integers::between(2, 100))
            ->then(function($count) {
                $this->hold(
                    Hold::count($count),
                    \range(0, $count - 1),
                );
            });
        $this->doesntHold(
            Hold::count(1),
            [],
        );
    }

    public function testNotCount()
    {
        $this->hold(
            Hold::notCount(1),
            [],
        );
        $this
            ->forAll(Set\Integers::between(2, 100))
            ->then(function($count) {
                $this->doesntHold(
                    Hold::notCount($count),
                    \range(0, $count - 1),
                );
            });
    }

    public function testGreaterThan()
    {
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::above(1),
            )
            ->then(function($a, $extra) {
                $this->hold(
                    Hold::greaterThan($a),
                    $a + $extra,
                );
            });
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::above(0),
            )
            ->then(function($a, $subtract) {
                $this->doesntHold(
                    Hold::greaterThan($a),
                    $a - $subtract,
                );
            });
    }

    public function testGreaterThanOrEqual()
    {
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::above(0),
            )
            ->then(function($a, $extra) {
                $this->hold(
                    Hold::greaterThanOrEqual($a),
                    $a + $extra,
                );
            });
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::above(1),
            )
            ->then(function($a, $subtract) {
                $this->doesntHold(
                    Hold::greaterThanOrEqual($a),
                    $a - $subtract,
                );
            });
    }

    public function testLessThan()
    {
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::above(1),
            )
            ->then(function($a, $subtract) {
                $this->hold(
                    Hold::lessThan($a),
                    $a - $subtract,
                );
            });
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::above(0),
            )
            ->then(function($a, $extra) {
                $this->doesntHold(
                    Hold::lessThan($a),
                    $a + $extra,
                );
            });
    }

    public function testLessThanOrEqual()
    {
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::above(0),
            )
            ->then(function($a, $subtract) {
                $this->hold(
                    Hold::lessThanOrEqual($a),
                    $a - $subtract,
                );
            });
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Integers::above(1),
            )
            ->then(function($a, $extra) {
                $this->doesntHold(
                    Hold::lessThanOrEqual($a),
                    $a + $extra,
                );
            });
    }

    public function testStringStartsWith()
    {
        $this
            ->forAll(
                Set\Unicode::strings(),
                Set\Unicode::strings(),
            )
            ->then(function($start, $string) {
                $this->hold(
                    Hold::stringStartsWith($string),
                    $string,
                );
                $this->hold(
                    Hold::stringStartsWith($start),
                    $start.$string,
                );
            });
        $this
            ->forAll(
                Set\Unicode::lengthBetween(1, 128),
                Set\Unicode::strings(),
            )
            ->then(function($start, $string) {
                $this->doesntHold(
                    Hold::stringStartsWith($start),
                    $string,
                );
            });
    }

    public function testStringDoesntStartWith()
    {
        $this
            ->forAll(
                Set\Unicode::lengthBetween(1, 128),
                Set\Unicode::strings(),
            )
            ->then(function($start, $string) {
                $this->hold(
                    Hold::stringDoesntStartWith($start),
                    $string,
                );
            });
        $this
            ->forAll(
                Set\Unicode::strings(),
                Set\Unicode::strings(),
            )
            ->then(function($start, $string) {
                $this->doesntHold(
                    Hold::stringDoesntStartWith($string),
                    $string,
                );
                $this->doesntHold(
                    Hold::stringDoesntStartWith($start),
                    $start.$string,
                );
            });
    }

    public function testStringContains()
    {
        $this
            ->forAll(
                Set\Unicode::strings(),
                Set\Unicode::strings(),
                Set\Unicode::strings(),
            )
            ->then(function($a, $b, $c) {
                $this->hold(
                    Hold::stringContains($a),
                    $a,
                );
                $this->hold(
                    Hold::stringContains($a),
                    $a.$b,
                );
                $this->hold(
                    Hold::stringContains($b),
                    $a.$b,
                );
                $this->hold(
                    Hold::stringContains($b),
                    $a.$b.$c,
                );
            });
        $this
            ->forAll(
                Set\Unicode::lengthBetween(1, 128),
                Set\Unicode::strings(),
            )
            ->then(function($a, $b) {
                $this->doesntHold(
                    Hold::stringContains($a),
                    $b,
                );
            });
    }

    public function testStringDoesntContain()
    {
        $this
            ->forAll(
                Set\Unicode::lengthBetween(1, 128),
                Set\Unicode::strings(),
            )
            ->then(function($a, $b) {
                $this->hold(
                    Hold::stringDoesntContain($a),
                    $b,
                );
            });
        $this
            ->forAll(
                Set\Unicode::strings(),
                Set\Unicode::strings(),
                Set\Unicode::strings(),
            )
            ->then(function($a, $b, $c) {
                $this->doesntHold(
                    Hold::stringDoesntContain($a),
                    $a,
                );
                $this->doesntHold(
                    Hold::stringDoesntContain($a),
                    $a.$b,
                );
                $this->doesntHold(
                    Hold::stringDoesntContain($b),
                    $a.$b,
                );
                $this->doesntHold(
                    Hold::stringDoesntContain($b),
                    $a.$b.$c,
                );
            });
    }

    public function testStringEndsWith()
    {
        $this
            ->forAll(
                Set\Unicode::strings(),
                Set\Unicode::strings(),
            )
            ->then(function($end, $string) {
                $this->hold(
                    Hold::stringEndsWith($string),
                    $string,
                );
                $this->hold(
                    Hold::stringEndsWith($end),
                    $string.$end,
                );
            });
        $this
            ->forAll(
                Set\Unicode::lengthBetween(1, 128),
                Set\Unicode::strings(),
            )
            ->then(function($end, $string) {
                $this->doesntHold(
                    Hold::stringEndsWith($end),
                    $string,
                );
            });
    }

    public function testStringDoesntEndWith()
    {
        $this
            ->forAll(
                Set\Unicode::lengthBetween(1, 128),
                Set\Unicode::strings(),
            )
            ->then(function($end, $string) {
                $this->hold(
                    Hold::stringDoesntEndWith($end),
                    $string,
                );
            });
        $this
            ->forAll(
                Set\Unicode::strings(),
                Set\Unicode::strings(),
            )
            ->then(function($end, $string) {
                $this->doesntHold(
                    Hold::stringDoesntEndWith($string),
                    $string,
                );
                $this->doesntHold(
                    Hold::stringDoesntEndWith($end),
                    $string.$end,
                );
            });
    }

    public function testSatisfies()
    {
        $this
            ->forAll(Set\AnyType::any())
            ->then(function($value) {
                $this->hold(
                    Hold::satisfies(static fn() => true),
                    $value,
                );
            });
        $this
            ->forAll(Set\AnyType::any())
            ->then(function($value) {
                $this->doesntHold(
                    Hold::satisfies(static fn() => false),
                    $value,
                );
            });
    }

    public function testDoesntSatisfy()
    {
        $this
            ->forAll(Set\AnyType::any())
            ->then(function($value) {
                $this->hold(
                    Hold::doesntSatisfy(static fn() => false),
                    $value,
                );
            });
        $this
            ->forAll(Set\AnyType::any())
            ->then(function($value) {
                $this->doesntHold(
                    Hold::doesntSatisfy(static fn() => true),
                    $value,
                );
            });
    }

    public function testSame()
    {
        $this
            ->forAll(Set\AnyType::any())
            ->then(function($value) {
                $this->hold(
                    Hold::same(static fn($a) => $a),
                    $value,
                    'of',
                    [$value],
                );
            });
        $this
            ->forAll(
                Set\AnyType::any(),
                Set\AnyType::any(),
            )
            ->filter(fn($a, $b) => $a !== $b)
            ->then(function($value, $not) {
                $this->doesntHold(
                    Hold::same(static fn() => $not),
                    $value,
                );
            });
    }

    public function testNotSame()
    {
        $this
            ->forAll(
                Set\AnyType::any(),
                Set\AnyType::any(),
            )
            ->filter(fn($a, $b) => $a !== $b)
            ->then(function($value, $not) {
                $this->hold(
                    Hold::notSame(static fn() => $not),
                    $value,
                );
            });
        $this
            ->forAll(Set\AnyType::any())
            ->then(function($value) {
                $this->doesntHold(
                    Hold::notSame(static fn($a) => $a),
                    $value,
                    'of',
                    [$value],
                );
            });
    }

    public function testIs()
    {
        $this
            ->forAll(Set\AnyType::any())
            ->then(function($value) {
                $this->hold(
                    Hold::is($value),
                    $value,
                );
            });
        $this
            ->forAll(
                Set\AnyType::any(),
                Set\AnyType::any(),
            )
            ->filter(fn($a, $b) => $a !== $b)
            ->then(function($value, $not) {
                $this->doesntHold(
                    Hold::is($not),
                    $value,
                );
            });
    }

    public function testNotIs()
    {
        $this
            ->forAll(
                Set\AnyType::any(),
                Set\AnyType::any(),
            )
            ->filter(fn($a, $b) => $a !== $b)
            ->then(function($value, $not) {
                $this->hold(
                    Hold::notIs($not),
                    $value,
                );
            });
        $this
            ->forAll(Set\AnyType::any())
            ->then(function($value) {
                $this->doesntHold(
                    Hold::notIs($value),
                    $value,
                );
            });
    }

    public function testMatches()
    {
        $this
            ->forAll(Set\Unicode::lengthBetween(1, 128))
            ->then(function($string) {
                $this->hold(
                    Hold::matches('~.+~'),
                    $string,
                );
            });
        $this->doesntHold(
            Hold::matches('~.+~'),
            '',
        );
    }

    public function testDoesntMatch()
    {
        $this->hold(
            Hold::doesntMatch('~.+~'),
            '',
        );
        $this
            ->forAll(Set\Unicode::lengthBetween(1, 128))
            ->then(function($string) {
                $this->doesntHold(
                    Hold::doesntMatch('~.+~'),
                    $string,
                );
            });
    }

    private function hold(Hold $hold, $value, string $kind = 'of', array $args = [])
    {
        $success = false;
        $failed = false;
        $held = static function() use (&$success) {
            $success = true;
        };
        $fail = static function() use (&$failed) {
            $failed = true;
        };

        $this->assertNull($hold(
            $held,
            $fail,
            TestResult::{$kind}(
                $value,
                new Arguments(Value::immutable([]), []),
            ),
            ...$args,
        ));
        $this->assertTrue($success);
        $this->assertFalse($failed);
    }

    private function doesntHold(Hold $hold, $value, string $kind = 'of', array $args = [])
    {
        $success = false;
        $failed = false;
        $held = static function() use (&$success) {
            $success = true;
        };
        $fail = static function() use (&$failed) {
            $failed = true;
        };

        $this->assertNull($hold(
            $held,
            $fail,
            TestResult::{$kind}(
                $value,
                new Arguments(Value::immutable([]), []),
            ),
            ...$args,
        ));
        $this->assertFalse($success);
        $this->assertTrue($failed);
    }
}
