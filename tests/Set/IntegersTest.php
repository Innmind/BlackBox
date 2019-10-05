<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Integers,
    Set,
};
use PHPUnit\Framework\TestCase;

class IntegersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new Integers
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(Integers::class, Integers::of());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = Integers::of()->reduce(
            [],
            static function(array $values, int $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertCount(100, $values);
    }

    public function testBoundsAreApplied()
    {
        $values = Integers::of(-10, 10);

        $hasOutsideBounds = $values->reduce(
            false,
            static function(bool $hasOutsideBounds, int $value): bool {
                return $hasOutsideBounds || $value > 10 || $value < -10;
            }
        );

        $this->assertFalse($hasOutsideBounds);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = Integers::of();
        $even = $integers->filter(static function(int $int): bool {
            return $int % 2 === 0;
        });

        $this->assertInstanceOf(Integers::class, $even);
        $this->assertNotSame($integers, $even);
        $hasOddInteger = $integers->reduce(
            false,
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            }
        );
        $this->assertTrue($hasOddInteger);

        $hasOddInteger = $even->reduce(
            false,
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            }
        );
        $this->assertFalse($hasOddInteger);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Integers::of();
        $b = $a->take(50);

        $this->assertInstanceOf(Integers::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(
            100,
            $a->reduce(
                [],
                static function(array $values, int $value): array {
                    $values[] = $value;

                    return $values;
                }
            )
        );
        $this->assertCount(
            50,
            $b->reduce(
                [],
                static function(array $values, int $value): array {
                    $values[] = $value;

                    return $values;
                }
            )
        );
    }

    public function testValues()
    {
        $a = Integers::of();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, \iterator_to_array($a->values()));
    }
}
