<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Strings,
    Set,
    Set\Value,
};

class StringsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, new Strings);
    }

    public function testAny()
    {
        $this->assertInstanceOf(Strings::class, Strings::any());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Strings::any()->values());

        $this->assertCount(100, $values);
    }

    public function testByDefaultMaxLengthIs128()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Strings::any()->values()),
        );

        $this->assertTrue(128 >= \max($values));
    }

    public function testMaxLengthIsParametrable()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Strings::any(256)->values()),
        );

        $this->assertTrue(256 >= \max($values));
        $this->assertTrue(\max($values) > 128);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Strings::any();
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(Strings::class, $others);
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
        $a = Strings::any();
        $b = $a->take(50);

        $this->assertInstanceOf(Strings::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values()));
        $this->assertCount(50, $this->unwrap($b->values()));
    }

    public function testValues()
    {
        $a = Strings::any();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, $this->unwrap($a->values()));

        foreach ($a->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }
}
