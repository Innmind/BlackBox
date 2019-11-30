<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Chars,
    Set,
};
use PHPUnit\Framework\TestCase;

class CharsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new Chars
        );
    }

    public function testAny()
    {
        $this->assertInstanceOf(Chars::class, Chars::any());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = \iterator_to_array(Chars::any()->values());

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
            \iterator_to_array($values->values()),
            static function(bool $hasOddChar, string $value): bool {
                return $hasOddChar || ord($value) % 2 === 1;
            },
            false
        );
        $this->assertTrue($hasOddChar);

        $hasOddChar = \array_reduce(
            \iterator_to_array($even->values()),
            static function(bool $hasOddChar, string $value): bool {
                return $hasOddChar || ord($value) % 2 === 1;
            },
            false
        );
        $this->assertFalse($hasOddChar);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Chars::any();
        $b = $a->take(50);

        $this->assertInstanceOf(Chars::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, \iterator_to_array($a->values()));
        $this->assertCount(50, \iterator_to_array($b->values()));
    }

    public function testValues()
    {
        $a = Chars::any();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, \iterator_to_array($a->values()));
    }
}
