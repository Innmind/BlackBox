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
            new Integers('a')
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(Integers::class, Integers::of('a'));
    }

    public function testName()
    {
        $this->assertSame('a', Integers::of('a')->name());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = Integers::of('a')->reduce(
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
        $values = Integers::of('a', -10, 10);

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
        $integers = Integers::of('a');
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
        $a = Integers::of('a');
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
}
