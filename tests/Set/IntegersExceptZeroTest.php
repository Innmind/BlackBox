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
            new IntegersExceptZero('a')
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(IntegersExceptZero::class, IntegersExceptZero::of('a'));
    }

    public function testName()
    {
        $this->assertSame('a', IntegersExceptZero::of('a')->name());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = IntegersExceptZero::of('a')->reduce(
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
        $integers = IntegersExceptZero::of('a');
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
        $a = IntegersExceptZero::of('a');
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
