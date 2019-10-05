<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Chars,
    Set,
};
use PHPUnit\Framework\TestCase;

class CharsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new Chars
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(Chars::class, Chars::of());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = Chars::of()->reduce(
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
        $values = Chars::of();
        $even = $values->filter(static function(string $value): bool {
            return \ord($value) % 2 === 0;
        });

        $this->assertInstanceOf(Chars::class, $even);
        $this->assertNotSame($values, $even);
        $hasOddChar = $values->reduce(
            false,
            static function(bool $hasOddChar, string $value): bool {
                return $hasOddChar || ord($value) % 2 === 1;
            }
        );
        $this->assertTrue($hasOddChar);

        $hasOddChar = $even->reduce(
            false,
            static function(bool $hasOddChar, string $value): bool {
                return $hasOddChar || ord($value) % 2 === 1;
            }
        );
        $this->assertFalse($hasOddChar);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Chars::of();
        $b = $a->take(50);

        $this->assertInstanceOf(Chars::class, $b);
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
        $a = Chars::of();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, \iterator_to_array($a->values()));
    }
}
