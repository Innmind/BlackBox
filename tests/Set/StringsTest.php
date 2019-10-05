<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Strings,
    Set,
};
use PHPUnit\Framework\TestCase;

class StringsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new Strings
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(Strings::class, Strings::of());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = Strings::of()->reduce(
            [],
            static function(array $values, string $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertCount(100, $values);
    }

    public function testByDefaultMaxLengthIs128()
    {
        $values = Strings::of()->reduce(
            [],
            static function(array $values, string $value): array {
                $values[] = \strlen($value);

                return $values;
            }
        );

        $this->assertTrue(128 >= \max($values));
    }

    public function testMaxLengthIsParametrable()
    {
        $values = Strings::of(256)->reduce(
            [],
            static function(array $values, string $value): array {
                $values[] = \strlen($value);

                return $values;
            }
        );

        $this->assertTrue(256 >= \max($values));
        $this->assertTrue(\max($values) > 128);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Strings::of();
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(Strings::class, $others);
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
        $a = Strings::of();
        $b = $a->take(50);

        $this->assertInstanceOf(Strings::class, $b);
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
        $a = Strings::of();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, \iterator_to_array($a->values()));
    }
}
