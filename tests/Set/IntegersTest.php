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
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, Integers::any());
    }

    public function testAny()
    {
        $this->assertInstanceOf(Set::class, Integers::any());
        $this->assertSame(\PHP_INT_MIN, Integers::implementation(null, null)->min());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = $this->unwrap(Integers::any()->values(Random::mersenneTwister));

        $this->assertCount(100, $values);
    }

    public function testBoundsAreApplied()
    {
        $values = Integers::between(-10, 10);

        $hasOutsideBounds = \array_reduce(
            $this->unwrap($values->values(Random::mersenneTwister)),
            static function(bool $hasOutsideBounds, int $value): bool {
                return $hasOutsideBounds || $value > 10 || $value < -10;
            },
            false,
        );

        $this->assertFalse($hasOutsideBounds);
    }

    public function testAbove()
    {
        $values = Integers::above(10);

        $this->assertInstanceOf(Set::class, $values);
        $this->assertCount(100, $this->unwrap($values->values(Random::mersenneTwister)));
        $this->assertGreaterThanOrEqual(
            10,
            \min($this->unwrap($values->values(Random::mersenneTwister))),
        );
    }

    public function testBelow()
    {
        $values = Integers::below(10);

        $this->assertInstanceOf(Set::class, $values);
        $this->assertCount(100, $this->unwrap($values->values(Random::mersenneTwister)));
        $this->assertLessThanOrEqual(
            10,
            \max($this->unwrap($values->values(Random::mersenneTwister))),
        );
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = Integers::any();
        $even = $integers->filter(static function(int $int): bool {
            return $int % 2 === 0;
        });

        $this->assertInstanceOf(Set::class, $even);
        $this->assertNotSame($integers, $even);
        $hasOddInteger = \array_reduce(
            $this->unwrap($integers->values(Random::mersenneTwister)),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false,
        );
        $this->assertTrue($hasOddInteger);

        $hasOddInteger = \array_reduce(
            $this->unwrap($even->values(Random::mersenneTwister)),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false,
        );
        $this->assertFalse($hasOddInteger);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Integers::any();
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($b->values(Random::mersenneTwister)));
    }

    public function testValues()
    {
        $a = Integers::any();

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testZeroCannotBeShrinked()
    {
        $ints = Integers::between(-1, 1)->filter(static fn($i) => $i === 0);

        foreach ($ints->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testIntegersCanBeShrinked()
    {
        $ints = Integers::any()->filter(static fn($i) => $i !== 0);

        foreach ($ints->values(Random::mersenneTwister) as $value) {
            $this->assertNotNull($value->shrink());
        }
    }

    public function testShrinkedIntegersAreImmutable()
    {
        $ints = Integers::any()->filter(static fn($i) => $i !== 0);

        foreach ($ints->values(Random::mersenneTwister) as $value) {
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testIntegersAreShrinkedTowardZero()
    {
        $positive = Integers::above(1);

        foreach ($positive->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertLessThan($value->unwrap(), $a->unwrap());
            $this->assertLessThan($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }

        $negative = Integers::below(-1);

        foreach ($negative->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertGreaterThan($value->unwrap(), $a->unwrap());
            $this->assertGreaterThan($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }
    }

    public function testShrinkedValuesNeverChangeSign()
    {
        $integers = Integers::any();

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

    public function testShrinkedValuesAlwaysRespectThePredicate()
    {
        $even = Integers::any()->filter(static fn($i) => $i !== 0 && ($i % 2) === 0);

        foreach ($even->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            $this->assertSame(0, $dichotomy->a()->unwrap() % 2);
            $this->assertSame(0, $dichotomy->b()->unwrap() % 2);
        }

        $odd = Integers::any()->filter(static fn($i) => $i !== 0 && ($i % 2) === 1);

        foreach ($odd->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            $this->assertSame(1, $dichotomy->a()->unwrap() % 2);
            $this->assertSame(1, $dichotomy->b()->unwrap() % 2);
        }
    }

    public function testShrinkingStrategiesNeverProduceTheSameResultTwice()
    {
        foreach (Integers::between(-1000, 1000)->values(Random::mersenneTwister) as $integer) {
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

        foreach (Integers::between(-1000, 1000)->values(Random::mersenneTwister) as $integer) {
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
        $integers = Integers::between(1000, 2000);

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

    public function testTakeNoElement()
    {
        $this->assertCount(
            0,
            \iterator_to_array(
                Integers::any()
                    ->take(0)
                    ->values(Random::mersenneTwister),
            ),
        );
    }

    public function testStrategyAAlwaysLeadToSmallestValuePossible()
    {
        $ints = Integers::above(43);

        foreach ($ints->values(Random::mersenneTwister) as $int) {
            while ($shrunk = $int->shrink()) {
                $int = $shrunk->a();
            }

            $this->assertSame(43, $int->unwrap());
        }
    }
}
