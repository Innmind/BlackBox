<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Integers,
    Set,
    Set\Value,
    Random,
};

class IntegersTest extends TestCase
{
    public function testAny()
    {
        $this->assertSame(\PHP_INT_MIN, Integers::implementation(null, null)->min());
    }

    public function testBoundsAreApplied()
    {
        $values = Set::integers()->between(-10, 10)->take(100);

        $hasOutsideBounds = \array_reduce(
            $this->unwrap($values),
            static function(bool $hasOutsideBounds, int $value): bool {
                return $hasOutsideBounds || $value > 10 || $value < -10;
            },
            false,
        );

        $this->assertFalse($hasOutsideBounds);
    }

    public function testAbove()
    {
        $values = Set::integers()->above(10)->take(100);

        $this->assertInstanceOf(Set::class, $values);
        $this->assertCount(100, $this->unwrap($values));
        $this->assertGreaterThanOrEqual(
            10,
            \min($this->unwrap($values)),
        );
    }

    public function testBelow()
    {
        $values = Set::integers()->below(10)->take(100);

        $this->assertInstanceOf(Set::class, $values);
        $this->assertCount(100, $this->unwrap($values));
        $this->assertLessThanOrEqual(
            10,
            \max($this->unwrap($values)),
        );
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = Set::integers()->take(100);
        $even = $integers->filter(static function(int $int): bool {
            return $int % 2 === 0;
        });

        $this->assertInstanceOf(Set::class, $even);
        $this->assertNotSame($integers, $even);
        $hasOddInteger = \array_reduce(
            $this->unwrap($integers),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false,
        );
        $this->assertTrue($hasOddInteger);

        $hasOddInteger = \array_reduce(
            $this->unwrap($even),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false,
        );
        $this->assertFalse($hasOddInteger);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Set::integers()->take(100);
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a));
        $this->assertCount(50, $this->unwrap($b));
    }

    public function testValues()
    {
        $a = Set::integers()->take(100);

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }

    public function testZeroCannotBeShrunk()
    {
        $ints = Set::integers()
            ->between(-1, 1)
            ->filter(static fn($i) => $i === 0)
            ->take(100);

        foreach ($ints->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testIntegersCanBeShrunk()
    {
        $ints = Set::integers()
            ->filter(static fn($i) => $i !== 0)
            ->take(100);

        foreach ($ints->values(Random::mersenneTwister) as $value) {
            $this->assertNotNull($value->shrink());
        }
    }

    public function testIntegersAreShrunkTowardZero()
    {
        $positive = Set::integers()
            ->above(1)
            ->take(100);

        foreach ($positive->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertLessThan($value->unwrap(), $a->unwrap());
            $this->assertLessThan($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }

        $negative = Set::integers()
            ->below(-1)
            ->take(100);

        foreach ($negative->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertGreaterThan($value->unwrap(), $a->unwrap());
            $this->assertGreaterThan($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }
    }

    public function testShrunkValuesNeverChangeSign()
    {
        $integers = Set::integers()->take(100);

        foreach ($integers->values(Random::mersenneTwister) as $value) {
            $this->assertSame(
                $value->unwrap() <=> 0,
                $value->shrink()->a()->unwrap() <=> 0,
            );
            $this->assertSame(
                $value->unwrap() <=> 0,
                $value->shrink()->b()->unwrap() <=> 0,
            );
        }
    }

    public function testShrunkValuesAlwaysRespectThePredicate()
    {
        $even = Set::integers()
            ->filter(static fn($i) => $i !== 0 && ($i % 2) === 0)
            ->take(100);

        foreach ($even->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            if (\is_null($dichotomy)) {
                continue;
            }

            $this->assertSame(0, $dichotomy->a()->unwrap() % 2);
            $this->assertSame(0, $dichotomy->b()->unwrap() % 2);
        }

        $odd = Set::integers()
            ->filter(static fn($i) => $i !== 0 && ($i % 2) === 1)
            ->take(100);

        foreach ($odd->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            if (\is_null($dichotomy)) {
                continue;
            }

            $this->assertSame(1, $dichotomy->a()->unwrap() % 2);
            $this->assertSame(1, $dichotomy->b()->unwrap() % 2);
        }
    }

    public function testShrinkingStrategiesNeverProduceTheSameResultTwice()
    {
        foreach (Set::integers()->between(-1000, 1000)->toSet()->values(Random::mersenneTwister) as $integer) {
            if ($integer->shrink()) {
                break;
            }
        }

        $previous = $integer;
        $integer = $integer->shrink()->a();

        while ($shrunk = $integer->shrink()) {
            $this->assertNotSame($previous->unwrap(), $integer->unwrap());
            $previous = $integer;
            $integer = $shrunk->a();
        }

        foreach (Set::integers()->between(-1000, 1000)->toSet()->values(Random::mersenneTwister) as $integer) {
            if ($integer->shrink()) {
                break;
            }
        }

        $previous = $integer;
        $integer = $integer->shrink()->b();

        do {
            $this->assertNotSame($previous->unwrap(), $integer->unwrap());

            if (!$integer->shrink()) {
                return;
            }

            $previous = $integer;
            $integer = $integer->shrink()->b();
        } while ($integer?->shrink() ?? false);
    }

    public function testInitialBoundsAreAlwaysRespectedWhenShrinking()
    {
        $integers = Set::integers()
            ->between(1000, 2000)
            ->take(100);

        $assertInBounds = function(Value $value, string $strategy) {
            while ($shrunk = $value->shrink()) {
                $this->assertGreaterThanOrEqual(1000, $value->unwrap());
                $this->assertLessThanOrEqual(2000, $value->unwrap());
                $value = $shrunk->$strategy();
            }
        };

        foreach ($integers->values(Random::mersenneTwister) as $value) {
            $assertInBounds($value, 'a');
            $assertInBounds($value, 'b');
        }
    }

    public function testStrategyAAlwaysLeadToSmallestValuePossible()
    {
        $ints = Set::integers()
            ->above(43)
            ->take(100);

        foreach ($ints->values(Random::mersenneTwister) as $int) {
            while ($shrunk = $int->shrink()) {
                $int = $shrunk->a();
            }

            $this->assertSame(43, $int->unwrap());
        }
    }
}
