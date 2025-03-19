<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Chars,
    Set,
    Set\Value,
    Random,
};

class CharsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, Chars::any());
    }

    public function testAny()
    {
        $this->assertInstanceOf(Set::class, Chars::any());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Chars::any()->values(Random::mersenneTwister));

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Chars::any();
        $even = $values->filter(static function(string $value): bool {
            return \ord($value) % 2 === 0;
        });

        $this->assertInstanceOf(Set::class, $even);
        $this->assertNotSame($values, $even);
        $hasOddChar = \array_reduce(
            $this->unwrap($values->values(Random::mersenneTwister)),
            static function(bool $hasOddChar, string $value): bool {
                return $hasOddChar || \ord($value) % 2 === 1;
            },
            false,
        );
        $this->assertTrue($hasOddChar);

        $hasOddChar = \array_reduce(
            $this->unwrap($even->values(Random::mersenneTwister)),
            static function(bool $hasOddChar, string $value): bool {
                return $hasOddChar || \ord($value) % 2 === 1;
            },
            false,
        );
        $this->assertFalse($hasOddChar);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Chars::any();
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($b->values(Random::mersenneTwister)));
    }

    public function testValues()
    {
        $a = Chars::any();

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->immutable());
        }
    }

    public function testCharsAreShrinkable()
    {
        $chars = Chars::any();

        foreach ($chars->values(Random::mersenneTwister) as $value) {
            if ($value->unwrap() === \chr(0)) {
                // since chars is a decoration of integers chr(0) cannot be
                // shrunk as it is the lowest value of the integer set
                continue;
            }

            $this->assertNotNull($value->shrink());
        }
    }

    public function testLowercaseLetter()
    {
        $allowed = \range('a', 'z');

        foreach (Chars::lowercaseLetter()->values(Random::mersenneTwister) as $value) {
            $this->assertContains($value->unwrap(), $allowed);
        }
    }

    public function testUppercaseLetter()
    {
        $allowed = \range('A', 'Z');

        foreach (Chars::uppercaseLetter()->values(Random::mersenneTwister) as $value) {
            $this->assertContains($value->unwrap(), $allowed);
        }
    }

    public function testNumber()
    {
        $allowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        foreach (Chars::number()->values(Random::mersenneTwister) as $value) {
            $this->assertContains($value->unwrap(), $allowed);
        }
    }

    public function testAscii()
    {
        $allowed = \range(' ', '~');

        foreach (Chars::ascii()->values(Random::mersenneTwister) as $value) {
            $this->assertContains($value->unwrap(), $allowed);
        }
    }
}
