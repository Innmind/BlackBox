<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Integers,
    Set,
    Set\Value,
    Random\MtRand,
};

class IntegersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, Integers::any());
    }

    public function testAny()
    {
        $this->assertInstanceOf(Integers::class, Integers::any());
        $this->assertSame(\PHP_INT_MIN, Integers::any()->lowerBound());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = $this->unwrap(Integers::any()->values(new MtRand));

        $this->assertCount(100, $values);
    }

    public function testBoundsAreApplied()
    {
        $values = Integers::between(-10, 10);

        $hasOutsideBounds = \array_reduce(
            $this->unwrap($values->values(new MtRand)),
            static function(bool $hasOutsideBounds, int $value): bool {
                return $hasOutsideBounds || $value > 10 || $value < -10;
            },
            false,
        );

        $this->assertSame(-10, $values->lowerBound());
        $this->assertFalse($hasOutsideBounds);
    }

    public function testAbove()
    {
        $values = Integers::above(10);

        $this->assertInstanceOf(Integers::class, $values);
        $this->assertCount(100, $this->unwrap($values->values(new MtRand)));
        $this->assertGreaterThanOrEqual(
            10,
            \min($this->unwrap($values->values(new MtRand))),
        );
    }

    public function testBelow()
    {
        $values = Integers::below(10);

        $this->assertInstanceOf(Integers::class, $values);
        $this->assertCount(100, $this->unwrap($values->values(new MtRand)));
        $this->assertLessThanOrEqual(
            10,
            \max($this->unwrap($values->values(new MtRand))),
        );
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = Integers::any();
        $even = $integers->filter(static function(int $int): bool {
            return $int % 2 === 0;
        });

        $this->assertInstanceOf(Integers::class, $even);
        $this->assertNotSame($integers, $even);
        $hasOddInteger = \array_reduce(
            $this->unwrap($integers->values(new MtRand)),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false,
        );
        $this->assertTrue($hasOddInteger);

        $hasOddInteger = \array_reduce(
            $this->unwrap($even->values(new MtRand)),
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

        $this->assertInstanceOf(Integers::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(new MtRand)));
        $this->assertCount(50, $this->unwrap($b->values(new MtRand)));
    }

    public function testValues()
    {
        $a = Integers::any();

        $this->assertInstanceOf(\Generator::class, $a->values(new MtRand));
        $this->assertCount(100, $this->unwrap($a->values(new MtRand)));

        foreach ($a->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testZeroCannotBeShrinked()
    {
        $ints = Integers::between(-1, 1)->filter(fn($i) => $i === 0);

        foreach ($ints->values(new MtRand) as $value) {
            $this->assertFalse($value->shrinkable());
        }
    }

    public function testIntegersCanBeShrinked()
    {
        $ints = Integers::any()->filter(fn($i) => $i !== 0);

        foreach ($ints->values(new MtRand) as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }

    public function testShrinkedIntegersAreImmutable()
    {
        $ints = Integers::any()->filter(fn($i) => $i !== 0);

        foreach ($ints->values(new MtRand) as $value) {
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testIntegersAreShrinkedTowardZero()
    {
        $positive = Integers::above(1);

        foreach ($positive->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertLessThan($value->unwrap(), $a->unwrap());
            $this->assertLessThan($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }

        $negative = Integers::below(-1);

        foreach ($negative->values(new MtRand) as $value) {
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

        foreach ($integers->values(new MtRand) as $value) {
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
        $even = Integers::any()->filter(fn($i) => $i !== 0 && ($i % 2) === 0);

        foreach ($even->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();

            $this->assertSame(0, $dichotomy->a()->unwrap() % 2);
            $this->assertSame(0, $dichotomy->b()->unwrap() % 2);
        }

        $odd = Integers::any()->filter(fn($i) => $i !== 0 && ($i % 2) === 1);

        foreach ($odd->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();

            $this->assertSame(1, $dichotomy->a()->unwrap() % 2);
            $this->assertSame(1, $dichotomy->b()->unwrap() % 2);
        }
    }

    public function testShrinkingStrategiesNeverProduceTheSameResultTwice()
    {
        $integer = Integers::between(-1000, 1000)->values(new MtRand)->current();
        $previous = $integer;
        $integer = $integer->shrink()->a();

        while ($integer->shrinkable()) {
            $this->assertNotSame($previous->unwrap(), $integer->unwrap());
            $previous = $integer;
            $integer = $integer->shrink()->a();
        }

        $integer = Integers::between(-1000, 1000)->values(new MtRand)->current();
        $previous = $integer;
        $integer = $integer->shrink()->b();

        do {
            $this->assertNotSame($previous->unwrap(), $integer->unwrap());
            $previous = $integer;
            $integer = $integer->shrink()->b();
        } while ($integer->shrinkable());
    }

    public function testInitialBoundsAreAlwaysRespectedWhenShrinking()
    {
        $integers = Integers::between(1000, 2000);

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
}
