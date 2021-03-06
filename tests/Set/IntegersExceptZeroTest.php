<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\IntegersExceptZero,
    Set,
    Set\Value,
    Random\MtRand,
};

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
        $values = $this->unwrap(IntegersExceptZero::any()->values(new MtRand));

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
        $a = IntegersExceptZero::any();
        $b = $a->take(50);

        $this->assertInstanceOf(IntegersExceptZero::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(new MtRand)));
        $this->assertCount(50, $this->unwrap($b->values(new MtRand)));
    }

    public function testValues()
    {
        $a = IntegersExceptZero::any();

        $this->assertInstanceOf(\Generator::class, $a->values(new MtRand));
        $this->assertCount(100, $this->unwrap($a->values(new MtRand)));

        foreach ($a->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testShrinkable()
    {
        $integers = IntegersExceptZero::any();

        foreach ($integers->values(new MtRand) as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }
}
