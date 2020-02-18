<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Chars,
    Set,
    Set\Value,
};

class CharsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, new Chars);
    }

    public function testAny()
    {
        $this->assertInstanceOf(Chars::class, Chars::any());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Chars::any()->values());

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Chars::any();
        $even = $values->filter(static function(string $value): bool {
            return \ord($value) % 2 === 0;
        });

        $this->assertInstanceOf(Chars::class, $even);
        $this->assertNotSame($values, $even);
        $hasOddChar = \array_reduce(
            $this->unwrap($values->values()),
            static function(bool $hasOddChar, string $value): bool {
                return $hasOddChar || ord($value) % 2 === 1;
            },
            false,
        );
        $this->assertTrue($hasOddChar);

        $hasOddChar = \array_reduce(
            $this->unwrap($even->values()),
            static function(bool $hasOddChar, string $value): bool {
                return $hasOddChar || ord($value) % 2 === 1;
            },
            false,
        );
        $this->assertFalse($hasOddChar);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Chars::any();
        $b = $a->take(50);

        $this->assertInstanceOf(Chars::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values()));
        $this->assertCount(50, $this->unwrap($b->values()));
    }

    public function testValues()
    {
        $a = Chars::any();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, $this->unwrap($a->values()));

        foreach ($a->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testCharsAreNotShrinkable()
    {
        $chars = Chars::any();

        foreach ($chars->values() as $value) {
            $this->assertFalse($value->shrinkable());
        }
    }
}
