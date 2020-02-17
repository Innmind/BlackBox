<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Strings,
    Set\Regex,
    Set,
    Set\Value,
};

class StringsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, new Strings);
    }

    public function testAny()
    {
        $this->assertInstanceOf(Strings::class, Strings::any());
    }

    public function testMatching()
    {
        $this->assertInstanceOf(Regex::class, Strings::matching('\d'));
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Strings::any()->values());

        $this->assertCount(100, $values);
    }

    public function testByDefaultMaxLengthIs128()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Strings::any()->values()),
        );

        $this->assertLessThanOrEqual(128, \max($values));
    }

    public function testMaxLengthIsParametrable()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Strings::any(256)->values()),
        );

        $this->assertLessThanOrEqual(256, \max($values));
        $this->assertGreaterThan(128, \max($values));
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Strings::any();
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(Strings::class, $others);
        $this->assertNotSame($values, $others);
        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($values->values()),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertTrue($hasLengthAbove10);

        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($others->values()),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertFalse($hasLengthAbove10);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Strings::any();
        $b = $a->take(50);

        $this->assertInstanceOf(Strings::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values()));
        $this->assertCount(50, $this->unwrap($b->values()));
    }

    public function testValues()
    {
        $a = Strings::any();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, $this->unwrap($a->values()));

        foreach ($a->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testEmptyStringCannotBeShrinked()
    {
        $strings = new Strings(1); // always generate string of length 1

        foreach ($strings->values() as $value) {
            $this->assertFalse(
                $value
                    ->shrink()
                    ->a() // length of 0
                    ->shrinkable()
            );
        }
    }

    public function testNonEmptyStringsAreShrinkable()
    {
        $strings = Strings::any()->filter(fn($string) => $string !== '');

        foreach ($strings->values() as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }

    public function testShrinkedValuesAreImmutable()
    {
        $strings = Strings::any()->filter(fn($string) => $string !== '');

        foreach ($strings->values() as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertTrue($a->isImmutable());
            $this->assertTrue($b->isImmutable());
        }
    }

    public function testStringsAreShrinkedFromBothEnds()
    {
        $strings = Strings::any()->filter(fn($string) => $string !== '');

        foreach ($strings->values() as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            if (strlen($value->unwrap()) === 1) {
                // because it will shrink to the identity value because the shrunk
                // empty string wouldn't match the predicate
                continue;
            }

            $this->assertNotSame($a->unwrap(), $value->unwrap());
            $this->assertStringStartsWith($a->unwrap(), $value->unwrap());
            $this->assertNotSame($b->unwrap(), $value->unwrap());
            $this->assertStringEndsWith($b->unwrap(), $value->unwrap());
        }
    }

    public function testShrinkedValuesAlwaysMatchTheGivenPredicate()
    {
        $strings = Strings::any()->filter(fn($string) => strlen($string) > 20);

        foreach ($strings->values() as $value) {
            $dichotomy = $value->shrink();

            $this->assertTrue(strlen($dichotomy->a()->unwrap()) > 20);
            $this->assertTrue(strlen($dichotomy->b()->unwrap()) > 20);
        }
    }
}
