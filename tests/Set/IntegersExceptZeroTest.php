<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\IntegersExceptZero,
    Set,
};
use PHPUnit\Framework\TestCase;

class IntegersExceptZeroTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, new IntegersExceptZero);
    }

    public function testAny()
    {
        $this->assertInstanceOf(IntegersExceptZero::class, IntegersExceptZero::any());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = \iterator_to_array(IntegersExceptZero::any()->values());

        $this->assertCount(100, $values);
        $this->assertNotContains(0, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = IntegersExceptZero::any();
        $even = $integers->filter(static function(int $int): bool {
            return $int % 2 === 0;
        });

        $this->assertInstanceOf(IntegersExceptZero::class, $even);
        $this->assertNotSame($integers, $even);
        $hasOddInteger = \array_reduce(
            \iterator_to_array($integers->values()),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false,
        );
        $this->assertTrue($hasOddInteger);

        $hasOddInteger = \array_reduce(
            \iterator_to_array($even->values()),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false,
        );
        $this->assertFalse($hasOddInteger);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = IntegersExceptZero::any();
        $b = $a->take(50);

        $this->assertInstanceOf(IntegersExceptZero::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, \iterator_to_array($a->values()));
        $this->assertCount(50, \iterator_to_array($b->values()));
    }

    public function testValues()
    {
        $a = IntegersExceptZero::any();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, \iterator_to_array($a->values()));
    }
}
