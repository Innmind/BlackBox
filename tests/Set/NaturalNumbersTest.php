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
            new NaturalNumbers('a')
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(NaturalNumbers::class, NaturalNumbers::of('a'));
    }

    public function testName()
    {
        $this->assertSame('a', NaturalNumbers::of('a')->name());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = NaturalNumbers::of('a')->reduce(
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
        $integers = NaturalNumbers::of('a');
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
        $a = NaturalNumbers::of('a');
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
}
