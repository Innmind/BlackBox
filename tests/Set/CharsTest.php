<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Value,
    Random,
};

class CharsTest extends TestCase
{
    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Set::strings()->chars()->toSet()->values(Random::mersenneTwister));

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Set::strings()->chars()->toSet();
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
        $a = Set::strings()->chars()->toSet();
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($b->values(Random::mersenneTwister)));
    }

    public function testValues()
    {
        $a = Set::strings()->chars()->toSet();

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->immutable());
        }
    }

    public function testCharsAreShrinkable()
    {
        $chars = Set::strings()->chars()->toSet();

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

        foreach (Set::strings()->chars()->lowercaseLetter()->values(Random::mersenneTwister) as $value) {
            $this->assertContains($value->unwrap(), $allowed);
        }
    }

    public function testUppercaseLetter()
    {
        $allowed = \range('A', 'Z');

        foreach (Set::strings()->chars()->uppercaseLetter()->values(Random::mersenneTwister) as $value) {
            $this->assertContains($value->unwrap(), $allowed);
        }
    }

    public function testNumber()
    {
        $allowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        foreach (Set::strings()->chars()->number()->values(Random::mersenneTwister) as $value) {
            $this->assertContains($value->unwrap(), $allowed);
        }
    }

    public function testAscii()
    {
        $allowed = \range(' ', '~');

        foreach (Set::strings()->chars()->ascii()->values(Random::mersenneTwister) as $value) {
            $this->assertContains($value->unwrap(), $allowed);
        }
    }
}
