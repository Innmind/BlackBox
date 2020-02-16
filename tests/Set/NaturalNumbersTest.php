<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\NaturalNumbers,
    Set,
    Set\Value,
};

class NaturalNumbersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, new NaturalNumbers);
    }

    public function testAny()
    {
        $this->assertInstanceOf(NaturalNumbers::class, NaturalNumbers::any());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = $this->unwrap(NaturalNumbers::any()->values());

        $this->assertCount(100, $values);

        foreach ($values as $value) {
            $this->assertTrue($value >= 0);
        }
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = NaturalNumbers::any();
        $even = $integers->filter(static function(int $int): bool {
            return $int % 2 === 0;
        });

        $this->assertInstanceOf(NaturalNumbers::class, $even);
        $this->assertNotSame($integers, $even);
        $hasOddInteger = \array_reduce(
            $this->unwrap($integers->values()),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false,
        );
        $this->assertTrue($hasOddInteger);

        $hasOddInteger = \array_reduce(
            $this->unwrap($even->values()),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false,
        );
        $this->assertFalse($hasOddInteger);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = NaturalNumbers::any();
        $b = $a->take(50);

        $this->assertInstanceOf(NaturalNumbers::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values()));
        $this->assertCount(50, $this->unwrap($b->values()));
    }

    public function testValues()
    {
        $a = NaturalNumbers::any();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, $this->unwrap($a->values()));

        foreach ($a->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testShrinkable()
    {
        $integers = NaturalNumbers::any();

        foreach ($integers->values() as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }
}
