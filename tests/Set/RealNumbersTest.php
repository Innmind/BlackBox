<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\RealNumbers,
    Set,
    Set\Value,
    Random\MtRand,
};

class RealNumbersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, RealNumbers::any());
    }

    public function testAny()
    {
        $this->assertInstanceOf(RealNumbers::class, RealNumbers::any());
    }

    public function testBetween()
    {
        $numbers = RealNumbers::between(-100, 100);

        $this->assertInstanceOf(RealNumbers::class, $numbers);

        foreach ($numbers->values(new MtRand) as $value) {
            $this->assertGreaterThanOrEqual(-100, $value->unwrap());
            $this->assertLessThanOrEqual(100, $value->unwrap());
        }
    }

    public function testAbove()
    {
        $numbers = RealNumbers::above(0);

        $this->assertInstanceOf(RealNumbers::class, $numbers);

        foreach ($numbers->values(new MtRand) as $value) {
            $this->assertGreaterThanOrEqual(0, $value->unwrap());
        }
    }

    public function testBelow()
    {
        $numbers = RealNumbers::below(0);

        $this->assertInstanceOf(RealNumbers::class, $numbers);

        foreach ($numbers->values(new MtRand) as $value) {
            $this->assertLessThanOrEqual(0, $value->unwrap());
        }
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = $this->unwrap(RealNumbers::any()->values(new MtRand));

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = RealNumbers::any();
        $positive = $values->filter(static function(float $float): bool {
            return $float > 0;
        });

        $this->assertInstanceOf(RealNumbers::class, $positive);
        $this->assertNotSame($values, $positive);
        $hasNegative = \array_reduce(
            $this->unwrap($values->values(new MtRand)),
            static function(bool $hasNegative, float $value): bool {
                return $hasNegative || $value <=0;
            },
            false,
        );
        $this->assertTrue($hasNegative);

        $hasNegative = \array_reduce(
            $this->unwrap($positive->values(new MtRand)),
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

        $this->assertInstanceOf(RealNumbers::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(new MtRand)));
        $this->assertCount(50, $this->unwrap($b->values(new MtRand)));
    }

    public function testValues()
    {
        $a = RealNumbers::any();

        $this->assertInstanceOf(\Generator::class, $a->values(new MtRand));
        $this->assertCount(100, $this->unwrap($a->values(new MtRand)));

        foreach ($a->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testZeroCannotBeShrinked()
    {
        $numbers = RealNumbers::between(-1, 1)->filter(static fn($i) => $i === 0.0);

        foreach ($numbers->values(new MtRand) as $value) {
            $this->assertFalse($value->shrinkable());
        }
    }

    public function testRealNumbersCanBeShrinked()
    {
        $numbers = RealNumbers::any()->filter(static fn($i) => $i !== 0.0);

        foreach ($numbers->values(new MtRand) as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }

    public function testShrinkedRealNumbersAreImmutable()
    {
        $numbers = RealNumbers::any()->filter(static fn($i) => $i !== 0.0);

        foreach ($numbers->values(new MtRand) as $value) {
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testRealNumbersAreShrinkedTowardZero()
    {
        $positive = RealNumbers::above(1);

        foreach ($positive->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertLessThanOrEqual($value->unwrap(), $a->unwrap());
            $this->assertLessThanOrEqual($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }

        $negative = RealNumbers::below(-1);

        foreach ($negative->values(new MtRand) as $value) {
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

        foreach ($numbers->values(new MtRand) as $value) {
            if (!$value->shrinkable()) {
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

        foreach ($even->values(new MtRand) as $value) {
            if (!$value->shrinkable()) {
                continue;
            }

            $dichotomy = $value->shrink();

            $this->assertSame(0, ((int) \round($dichotomy->a()->unwrap())) % 2);
            $this->assertSame(0, ((int) \round($dichotomy->b()->unwrap())) % 2);
        }

        $odd = RealNumbers::any()->filter(static fn($i) => $i !== 0 && (((int) \round($i)) % 2) === 1);

        foreach ($odd->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();

            $this->assertSame(1, ((int) \round($dichotomy->a()->unwrap())) % 2);
            $this->assertSame(1, ((int) \round($dichotomy->b()->unwrap())) % 2);
        }
    }

    public function testInitialBoundsAreAlwaysRespectedWhenShrinking()
    {
        $integers = RealNumbers::between(1000, 2000);

        $assertInBounds = function(Value $value, string $strategy) {
            while ($value->shrinkable()) {
                $this->assertGreaterThanOrEqual(1000, $value->unwrap());
                $this->assertLessThanOrEqual(2000, $value->unwrap());
                $value = $value->shrink()->$strategy();
            }
        };

        foreach ($integers->values(new MtRand) as $value) {
            $assertInBounds($value, 'a');
            $assertInBounds($value, 'b');
        }
    }

    public function testTakeNoElement()
    {
        $this->assertCount(
            0,
            \iterator_to_array(
                RealNumbers::any()
                    ->take(0)
                    ->values(new MtRand),
            ),
        );
    }

    public function testStrategyAAlwaysLeadToSmallestValuePossible()
    {
        $floats = RealNumbers::above(43);

        foreach ($floats->values(new MtRand) as $float) {
            while ($float->shrinkable()) {
                $float = $float->shrink()->a();
            }

            $this->assertSame(43, (int) $float->unwrap());
        }
    }
}
