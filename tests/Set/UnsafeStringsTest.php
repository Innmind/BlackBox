<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\UnsafeStrings,
    Set,
    Set\Value,
};

class UnsafeStringsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, new UnsafeStrings);
    }

    public function testAny()
    {
        $this->assertInstanceOf(UnsafeStrings::class, UnsafeStrings::any());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(UnsafeStrings::any()->values());

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = UnsafeStrings::any();
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(UnsafeStrings::class, $others);
        $this->assertNotSame($values, $others);
        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($values->values()),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertTrue($hasLengthAbove10);

        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($others->values()),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertFalse($hasLengthAbove10);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = UnsafeStrings::any();
        $b = $a->take(50);

        $this->assertInstanceOf(UnsafeStrings::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values()));
        $this->assertCount(50, $this->unwrap($b->values()));
    }

    public function testValues()
    {
        $a = UnsafeStrings::any();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, $this->unwrap($a->values()));

        foreach ($a->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testEmptyStringCannotBeShrinked()
    {
        $strings = UnsafeStrings::any()->filter(fn($string) => $string === '');

        foreach ($strings->values() as $value) {
            $this->assertFalse($value->shrinkable());
        }
    }

    public function testNonEmptyStringsAreShrinkable()
    {
        $strings = UnsafeStrings::any()->filter(fn($string) => $string !== '');

        foreach ($strings->values() as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }

    public function testShrinkedValuesAreImmutable()
    {
        $strings = UnsafeStrings::any()->filter(fn($string) => $string !== '');

        foreach ($strings->values() as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertTrue($a->isImmutable());
            $this->assertTrue($b->isImmutable());
        }
    }

    public function testStringsAreShrinkedFromBothEnds()
    {
        $strings = UnsafeStrings::any()->filter(fn($string) => $string !== '');

        foreach ($strings->values() as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertNotSame($a->unwrap(), $value->unwrap());
            $this->assertStringStartsWith($a->unwrap(), $value->unwrap());
            $this->assertNotSame($b->unwrap(), $value->unwrap());
            $this->assertStringEndsWith($b->unwrap(), $value->unwrap());
        }
    }
}
