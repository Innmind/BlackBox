<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\NaturalNumbers,
    Set,
};
use PHPUnit\Framework\TestCase;

class NaturalNumbersTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new NaturalNumbers
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(NaturalNumbers::class, NaturalNumbers::of());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = NaturalNumbers::of()->reduce(
            [],
            static function(array $values, int $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertCount(100, $values);

        foreach ($values as $value) {
            $this->assertTrue($value >= 0);
        }
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = NaturalNumbers::of();
        $even = $integers->filter(static function(int $int): bool {
            return $int % 2 === 0;
        });

        $this->assertInstanceOf(NaturalNumbers::class, $even);
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
        $a = NaturalNumbers::of();
        $b = $a->take(50);

        $this->assertInstanceOf(NaturalNumbers::class, $b);
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
        $a = NaturalNumbers::of();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, \iterator_to_array($a->values()));
    }
}
