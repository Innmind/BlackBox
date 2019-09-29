<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\RealNumbers,
    Set,
};
use PHPUnit\Framework\TestCase;

class RealNumbersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new RealNumbers('a')
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(RealNumbers::class, RealNumbers::of('a'));
    }

    public function testName()
    {
        $this->assertSame('a', RealNumbers::of('a')->name());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = RealNumbers::of('a')->reduce(
            [],
            static function(array $values, float $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = RealNumbers::of('a');
        $positive = $values->filter(static function(float $float): bool {
            return $float > 0;
        });

        $this->assertInstanceOf(RealNumbers::class, $positive);
        $this->assertNotSame($values, $positive);
        $hasNegative = $values->reduce(
            false,
            static function(bool $hasNegative, float $value): bool {
                return $hasNegative || $value <=0;
            }
        );
        $this->assertTrue($hasNegative);

        $hasNegative = $positive->reduce(
            false,
            static function(bool $hasNegative, float $value): bool {
                return $hasNegative || $value <= 0;
            }
        );
        $this->assertFalse($hasNegative);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = RealNumbers::of('a');
        $b = $a->take(50);

        $this->assertInstanceOf(RealNumbers::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(
            100,
            $a->reduce(
                [],
                static function(array $values, float $value): array {
                    $values[] = $value;

                    return $values;
                }
            )
        );
        $this->assertCount(
            50,
            $b->reduce(
                [],
                static function(array $values, float $value): array {
                    $values[] = $value;

                    return $values;
                }
            )
        );
    }
}
