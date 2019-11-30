<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\RealNumbers,
    Set,
};
use PHPUnit\Framework\TestCase;

class RealNumbersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new RealNumbers
        );
    }

    public function testAny()
    {
        $this->assertInstanceOf(RealNumbers::class, RealNumbers::any());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = \iterator_to_array(RealNumbers::any()->values());

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
            \iterator_to_array($values->values()),
            static function(bool $hasNegative, float $value): bool {
                return $hasNegative || $value <=0;
            },
            false
        );
        $this->assertTrue($hasNegative);

        $hasNegative = \array_reduce(
            \iterator_to_array($positive->values()),
            static function(bool $hasNegative, float $value): bool {
                return $hasNegative || $value <= 0;
            },
            false
        );
        $this->assertFalse($hasNegative);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = RealNumbers::any();
        $b = $a->take(50);

        $this->assertInstanceOf(RealNumbers::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, \iterator_to_array($a->values()));
        $this->assertCount(50, \iterator_to_array($b->values()));
    }

    public function testValues()
    {
        $a = RealNumbers::any();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, \iterator_to_array($a->values()));
    }
}
