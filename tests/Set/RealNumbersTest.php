<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Value,
    Random,
};

class RealNumbersTest extends TestCase
{
    public function testBetween()
    {
        $numbers = Set::realNumbers()->between(-100, 100)->take(100);

        $this->assertInstanceOf(Set::class, $numbers);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertGreaterThanOrEqual(-100, $value->unwrap());
            $this->assertLessThanOrEqual(100, $value->unwrap());
        }
    }

    public function testAbove()
    {
        $numbers = Set::realNumbers()->above(0)->take(100);

        $this->assertInstanceOf(Set::class, $numbers);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertGreaterThanOrEqual(0, $value->unwrap());
        }
    }

    public function testBelow()
    {
        $numbers = Set::realNumbers()->below(0)->take(100);

        $this->assertInstanceOf(Set::class, $numbers);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertLessThanOrEqual(0, $value->unwrap());
        }
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Set::realNumbers()->take(100);
        $positive = $values->filter(static function(float $float): bool {
            return $float > 0;
        });

        $this->assertInstanceOf(Set::class, $positive);
        $this->assertNotSame($values, $positive);
        $hasNegative = \array_reduce(
            $this->unwrap($values->values(Random::mersenneTwister)),
            static function(bool $hasNegative, float $value): bool {
                return $hasNegative || $value <=0;
            },
            false,
        );
        $this->assertTrue($hasNegative);

        $hasNegative = \array_reduce(
            $this->unwrap($positive->values(Random::mersenneTwister)),
            static function(bool $hasNegative, float $value): bool {
                return $hasNegative || $value <= 0;
            },
            false,
        );
        $this->assertFalse($hasNegative);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Set::realNumbers()->take(100);
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($b->values(Random::mersenneTwister)));
    }

    public function testValues()
    {
        $a = Set::realNumbers()->take(100);

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }

    public function testZeroCannotBeShrinked()
    {
        $number = Set::realNumbers()
            ->between(-1, 1)
            ->take(100)
            ->values(Random::mersenneTwister)
            ->current();

        while ($shrunk = $number->shrink()) {
            $number = $shrunk->a();
        }

        $this->assertSame(0, $number->unwrap());
    }

    public function testRealNumbersCanBeShrinked()
    {
        $numbers = Set::realNumbers()
            ->filter(static fn($i) => $i !== 0.0)
            ->take(100);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertNotNull($value->shrink());
        }
    }

    public function testRealNumbersAreShrinkedTowardZero()
    {
        $positive = Set::realNumbers()
            ->above(1)
            ->take(100);

        foreach ($positive->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertLessThanOrEqual($value->unwrap(), $a->unwrap());
            $this->assertLessThanOrEqual($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }

        $negative = Set::realNumbers()
            ->below(-1)
            ->take(100);

        foreach ($negative->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertGreaterThanOrEqual($value->unwrap(), $a->unwrap());
            $this->assertGreaterThanOrEqual($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }
    }

    public function testShrinkedValuesNeverChangeSign()
    {
        $numbers = Set::realNumbers()->take(100);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            if (!$value->shrink()) {
                // as 0 may be generated
                continue;
            }

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

    public function testShrinkedValuesAlwaysRespectThePredicate()
    {
        $even = Set::realNumbers()
            ->filter(static fn($i) => $i !== 0 && (((int) \round($i)) % 2) === 0)
            ->take(100);

        foreach ($even->values(Random::mersenneTwister) as $value) {
            if (!$value->shrink()) {
                continue;
            }

            $dichotomy = $value->shrink();

            $this->assertSame(0, ((int) \round($dichotomy->a()->unwrap())) % 2);
            $this->assertSame(0, ((int) \round($dichotomy->b()->unwrap())) % 2);
        }

        $odd = Set::realNumbers()
            ->filter(static fn($i) => $i !== 0 && (((int) \round($i)) % 2) === 1)
            ->take(100);

        foreach ($odd->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            if (\is_null($dichotomy)) {
                continue;
            }

            $this->assertSame(1, ((int) \round($dichotomy->a()->unwrap())) % 2);
            $this->assertSame(1, ((int) \round($dichotomy->b()->unwrap())) % 2);
        }
    }

    public function testInitialBoundsAreAlwaysRespectedWhenShrinking()
    {
        $integers = Set::realNumbers()
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
        $floats = Set::realNumbers()
            ->above(43)
            ->take(100);

        foreach ($floats->values(Random::mersenneTwister) as $float) {
            while ($shrunk = $float->shrink()) {
                $float = $shrunk->a();
            }

            $this->assertSame(43, (int) $float->unwrap());
        }
    }
}
