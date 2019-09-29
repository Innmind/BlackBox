<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\IntegersExceptZero,
    Set,
};
use PHPUnit\Framework\TestCase;

class IntegersExceptZeroTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new IntegersExceptZero
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(IntegersExceptZero::class, IntegersExceptZero::of());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = IntegersExceptZero::of()->reduce(
            [],
            static function(array $values, int $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertCount(100, $values);
        $this->assertNotContains(0, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = IntegersExceptZero::of();
        $even = $integers->filter(static function(int $int): bool {
            return $int % 2 === 0;
        });

        $this->assertInstanceOf(IntegersExceptZero::class, $even);
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
        $a = IntegersExceptZero::of();
        $b = $a->take(50);

        $this->assertInstanceOf(IntegersExceptZero::class, $b);
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
