<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Value,
    Random,
    Exception\EmptySet,
};

class UnsafeStringsTest extends TestCase
{
    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Set::strings()->unsafe()->values(Random::mersenneTwister));

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Set::strings()->unsafe();
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(Set::class, $others);
        $this->assertNotSame($values, $others);
        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($values->values(Random::mersenneTwister)),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertTrue($hasLengthAbove10);

        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($others->values(Random::mersenneTwister)),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertFalse($hasLengthAbove10);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Set::strings()->unsafe();
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($b->values(Random::mersenneTwister)));
    }

    public function testValues()
    {
        $a = Set::strings()->unsafe();

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }

    public function testEmptyStringCannotBeShrinked()
    {
        $strings = Set::strings()->unsafe()->filter(static fn($string) => $string === '');

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testNonEmptyStringsAreShrinkable()
    {
        $strings = Set::strings()->unsafe();

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            if ($value->unwrap() === '') {
                continue;
            }

            $this->assertNotNull($value->shrink());
        }
    }

    public function testStringsAreShrinkedFromBothEnds()
    {
        $strings = Set::strings()->unsafe()->filter(static fn($string) => \strlen($string) > 1);
        $shrunk = false;

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            if (\is_null($dichotomy)) {
                continue;
            }

            $a = $dichotomy->a();
            $b = $dichotomy->b();

            if (\strlen($value->unwrap()) === 2) {
                // we continue as the shrinked values won't match the set predicate
                continue;
            }

            $this->assertNotSame($a->unwrap(), $value->unwrap());
            $this->assertStringStartsWith($a->unwrap(), $value->unwrap());
            $this->assertNotSame($b->unwrap(), $value->unwrap());
            $this->assertStringEndsWith($b->unwrap(), $value->unwrap());
            $shrunk = true;
        }

        $this->assertTrue($shrunk, 'At least one string should have been shrunk');
    }

    public function testStringsOfOneCharacterCantBeShrunk()
    {
        // otherwise they won't match the given predicate
        $strings = Set::strings()->unsafe()->filter(static fn($string) => \strlen($string) === 1);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testShrinkedValuesAlwaysMatchTheGivenPredicate()
    {
        $strings = Set::strings()->unsafe()->filter(static fn($string) => \strlen($string) > 20);
        $shrunk = false;

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            if (\is_null($dichotomy)) {
                continue;
            }

            $this->assertTrue(\strlen($dichotomy->a()->unwrap()) > 20);
            $this->assertTrue(\strlen($dichotomy->b()->unwrap()) > 20);
            $shrunk = true;
        }

        $this->assertTrue($shrunk, 'At least one string should have been shrunk');
    }

    public function testThrowWhenCannotFindAValue()
    {
        $this->assert()->throws(
            static fn() => Set::strings()
                ->unsafe()
                ->filter(static fn() => false)
                ->values(Random::mersenneTwister)
                ->current(),
            EmptySet::class,
        );
    }
}
