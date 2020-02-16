<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\RealNumbers,
    Set,
    Set\Value,
};

class RealNumbersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, new RealNumbers);
    }

    public function testAny()
    {
        $this->assertInstanceOf(RealNumbers::class, RealNumbers::any());
    }

    public function testBetween()
    {
        $numbers = RealNumbers::between(-100, 100);

        $this->assertInstanceOf(RealNumbers::class, $numbers);

        foreach ($numbers->values() as $value) {
            $this->assertGreaterThanOrEqual(-100, $value->unwrap());
            $this->assertLessThanOrEqual(100, $value->unwrap());
        }
    }

    public function testAbove()
    {
        $numbers = RealNumbers::above(0);

        $this->assertInstanceOf(RealNumbers::class, $numbers);

        foreach ($numbers->values() as $value) {
            $this->assertGreaterThanOrEqual(0, $value->unwrap());
        }
    }

    public function testBelow()
    {
        $numbers = RealNumbers::below(0);

        $this->assertInstanceOf(RealNumbers::class, $numbers);

        foreach ($numbers->values() as $value) {
            $this->assertLessThanOrEqual(0, $value->unwrap());
        }
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = $this->unwrap(RealNumbers::any()->values());

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
            $this->unwrap($values->values()),
            static function(bool $hasNegative, float $value): bool {
                return $hasNegative || $value <=0;
            },
            false,
        );
        $this->assertTrue($hasNegative);

        $hasNegative = \array_reduce(
            $this->unwrap($positive->values()),
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
        $this->assertCount(100, $this->unwrap($a->values()));
        $this->assertCount(50, $this->unwrap($b->values()));
    }

    public function testValues()
    {
        $a = RealNumbers::any();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, $this->unwrap($a->values()));

        foreach ($a->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testZeroCannotBeShrinked()
    {
        $numbers = RealNumbers::between(-1, 1)->filter(fn($i) => $i === 0.0);

        foreach ($numbers->values() as $value) {
            $this->assertFalse($value->shrinkable());
        }
    }

    public function testRealNumbersCanBeShrinked()
    {
        $numbers = RealNumbers::any()->filter(fn($i) => $i !== 0.0);

        foreach ($numbers->values() as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }

    public function testShrinkedRealNumbersAreImmutable()
    {
        $numbers = RealNumbers::any()->filter(fn($i) => $i !== 0.0);

        foreach ($numbers->values() as $value) {
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testRealNumbersAreShrinkedTowardZero()
    {
        $positive = RealNumbers::above(1);

        foreach ($positive->values() as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertLessThanOrEqual($value->unwrap(), $a->unwrap());
            $this->assertLessThanOrEqual($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }

        $negative = RealNumbers::below(-1);

        foreach ($negative->values() as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertGreaterThanOrEqual($value->unwrap(), $a->unwrap());
            $this->assertGreaterThanOrEqual($value->unwrap(), $b->unwrap());
            $this->assertNotSame($a->unwrap(), $b->unwrap());
        }
    }
}
