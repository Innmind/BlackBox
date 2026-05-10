<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Value,
    Random,
};

class NaturalNumbersExceptZeroTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, Set::integers()->naturalNumbersExceptZero());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = $this->unwrap(
            Set::integers()
                ->naturalNumbersExceptZero()
                ->take(100),
        );

        $this->assertCount(100, $values);

        foreach ($values as $value) {
            $this->assertTrue($value > 0);
        }
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = Set::integers()->naturalNumbersExceptZero()->take(100);
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
        $a = Set::integers()->naturalNumbersExceptZero()->take(100);
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a));
        $this->assertCount(50, $this->unwrap($b));
    }

    public function testValues()
    {
        $a = Set::integers()->naturalNumbersExceptZero()->take(100);

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }

    public function testShrinkable()
    {
        $integers = Set::integers()->naturalNumbersExceptZero()->take(100);

        foreach ($integers->values(Random::mersenneTwister) as $value) {
            $this->assertNotNull($value->shrink());
        }
    }
}
