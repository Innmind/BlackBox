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
    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Set::strings()->unsafe()->take(100);
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(Set::class, $others);
        $this->assertNotSame($values, $others);
        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($values),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertTrue($hasLengthAbove10);

        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($others),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertFalse($hasLengthAbove10);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Set::strings()->unsafe()->take(100);
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a));
        $this->assertCount(50, $this->unwrap($b));
    }

    public function testValues()
    {
        $a = Set::strings()->unsafe()->take(100);

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }

    public function testEmptyStringCannotBeShrunk()
    {
        $strings = Set::strings()
            ->unsafe()
            ->filter(static fn($string) => $string === '')
            ->take(100);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testNonEmptyStringsAreShrinkable()
    {
        $strings = Set::strings()->unsafe()->take(100);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            if ($value->unwrap() === '') {
                continue;
            }

            $this->assertNotNull($value->shrink());
        }
    }

    public function testStringsAreShrunkFromBothEnds()
    {
        $strings = Set::strings()
            ->unsafe()
            ->filter(static fn($string) => \strlen($string) > 1)
            ->take(100);
        $shrunk = false;

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            if (\is_null($dichotomy)) {
                continue;
            }

            $a = $dichotomy->a();
            $b = $dichotomy->b();

            if (\strlen($value->unwrap()) === 2) {
                // we continue as the shrunk values won't match the set predicate
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
        $strings = Set::strings()
            ->unsafe()
            ->filter(static fn($string) => \strlen($string) === 1)
            ->take(100);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testShrunkValuesAlwaysMatchTheGivenPredicate()
    {
        $strings = Set::strings()
            ->unsafe()
            ->filter(static fn($string) => \strlen($string) > 20)
            ->take(100);
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
                ->take(100)
                ->values(Random::mersenneTwister)
                ->current(),
            EmptySet::class,
        );
    }
}
