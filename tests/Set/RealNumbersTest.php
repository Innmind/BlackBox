<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\RealNumbers,
    Set,
    Set\Value,
    Random,
};

class RealNumbersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, RealNumbers::any());
    }

    public function testBetween()
    {
        $numbers = RealNumbers::between(-100, 100);

        $this->assertInstanceOf(Set::class, $numbers);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertGreaterThanOrEqual(-100, $value->unwrap());
            $this->assertLessThanOrEqual(100, $value->unwrap());
        }
    }

    public function testAbove()
    {
        $numbers = RealNumbers::above(0);

        $this->assertInstanceOf(Set::class, $numbers);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertGreaterThanOrEqual(0, $value->unwrap());
        }
    }

    public function testBelow()
    {
        $numbers = RealNumbers::below(0);

        $this->assertInstanceOf(Set::class, $numbers);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertLessThanOrEqual(0, $value->unwrap());
        }
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = $this->unwrap(RealNumbers::any()->values(Random::mersenneTwister));

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = RealNumbers::any();
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
        $a = RealNumbers::any();
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($b->values(Random::mersenneTwister)));
    }

    public function testValues()
    {
        $a = RealNumbers::any();

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->immutable());
        }
    }

    public function testZeroCannotBeShrinked()
    {
        $numbers = RealNumbers::between(-1, 1)->filter(static fn($i) => $i === 0.0);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testRealNumbersCanBeShrinked()
    {
        $numbers = RealNumbers::any()->filter(static fn($i) => $i !== 0.0);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertNotNull($value->shrink());
        }
    }

    public function testShrinkedRealNumbersAreImmutable()
    {
        $numbers = RealNumbers::any()->filter(static fn($i) => $i !== 0.0);

        foreach ($numbers->values(Random::mersenneTwister) as $value) {
            $this->assertTrue($value->immutable());
        }
    }

    public function testRealNumbersAreShrinkedTowardZero()
    {
        $positive = RealNumbers::above(1);

        foreach ($positive->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertLessThanOrEqual($value->unwrap(), $a->unwrap());
            $this->assertLessThanOrEqual($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }

        $negative = RealNumbers::below(-1);

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
        $numbers = RealNumbers::any();

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
        $even = RealNumbers::any()->filter(static fn($i) => $i !== 0 && (((int) \round($i)) % 2) === 0);

        foreach ($even->values(Random::mersenneTwister) as $value) {
            if (!$value->shrink()) {
                continue;
            }

            $dichotomy = $value->shrink();

            $this->assertSame(0, ((int) \round($dichotomy->a()->unwrap())) % 2);
            $this->assertSame(0, ((int) \round($dichotomy->b()->unwrap())) % 2);
        }

        $odd = RealNumbers::any()->filter(static fn($i) => $i !== 0 && (((int) \round($i)) % 2) === 1);

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
        $integers = RealNumbers::between(1000, 2000);

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
        $floats = RealNumbers::above(43);

        foreach ($floats->values(Random::mersenneTwister) as $float) {
            while ($shrunk = $float->shrink()) {
                $float = $shrunk->a();
            }

            $this->assertSame(43, (int) $float->unwrap());
        }
    }
}
