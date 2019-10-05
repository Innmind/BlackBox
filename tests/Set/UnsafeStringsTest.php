<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\UnsafeStrings,
    Set,
};
use PHPUnit\Framework\TestCase;

class UnsafeStringsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new UnsafeStrings
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(UnsafeStrings::class, UnsafeStrings::of());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = UnsafeStrings::of()->reduce(
            [],
            static function(array $values, string $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = UnsafeStrings::of();
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(UnsafeStrings::class, $others);
        $this->assertNotSame($values, $others);
        $hasLengthAbove10 = $values->reduce(
            false,
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            }
        );
        $this->assertTrue($hasLengthAbove10);

        $hasLengthAbove10 = $others->reduce(
            false,
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            }
        );
        $this->assertFalse($hasLengthAbove10);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = UnsafeStrings::of();
        $b = $a->take(50);

        $this->assertInstanceOf(UnsafeStrings::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(
            100,
            $a->reduce(
                [],
                static function(array $values, string $value): array {
                    $values[] = $value;

                    return $values;
                }
            )
        );
        $this->assertCount(
            50,
            $b->reduce(
                [],
                static function(array $values, string $value): array {
                    $values[] = $value;

                    return $values;
                }
            )
        );
    }

    public function testValues()
    {
        $a = UnsafeStrings::of();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, \iterator_to_array($a->values()));
    }
}
