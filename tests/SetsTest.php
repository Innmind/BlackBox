<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use function Innmind\BlackBox\Set\{
    integers,
    integersExceptZero,
    naturalNumbers,
    naturalNumbersExceptZero,
    realNumbers,
    range,
    char,
    strings,
};
use Innmind\BlackBox\Exception\LogicException;
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class SetsTest extends TestCase
{
    public function testIntegers()
    {
        $set = integers(100);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('int', (string) $set->type());
        $this->assertCount(100, $set);

        $this->assertFalse($set->equals(integers(100)));
    }

    public function testThrowWhenIntegersRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        integers(0);
    }

    public function testIntegersExceptZero()
    {
        $set = integersExceptZero(100);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('int', (string) $set->type());
        $this->assertCount(100, $set);
        $this->assertFalse($set->contains(0));

        $this->assertFalse($set->equals(integersExceptZero(100)));
    }

    public function testThrowWhenIntegersExceptZeroRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        integersExceptZero(0);
    }

    public function testNaturalNumbers()
    {
        $set = naturalNumbers(100);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('int', (string) $set->type());
        $this->assertCount(100, $set);
        $lowest = $set
            ->sort(static function(int $a, int $b): int {
                return $a <=> $b;
            })
            ->first();
        $this->assertTrue($lowest >= 0);

        $this->assertFalse($set->equals(naturalNumbers(100)));
    }

    public function testThrowWhenNaturalNumbersRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        naturalNumbers(0);
    }

    public function testNaturalNumbersExceptZero()
    {
        $set = naturalNumbersExceptZero(100);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('int', (string) $set->type());
        $this->assertCount(100, $set);
        $lowest = $set
            ->sort(static function(int $a, int $b): int {
                return $a <=> $b;
            })
            ->first();
        $this->assertTrue($lowest >= 1);

        $this->assertFalse($set->equals(naturalNumbersExceptZero(100)));
    }

    public function testThrowWhenNaturalNumbersExceptZeroRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        naturalNumbersExceptZero(0);
    }

    public function testRealNumbers()
    {
        $set = realNumbers(100);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('float', (string) $set->type());
        $this->assertCount(100, $set);
        $lowest = $set
            ->sort(static function(float $a, float $b): int {
                return $a <=> $b;
            })
            ->first();
        $this->assertTrue($lowest < 0);

        $this->assertFalse($set->equals(realNumbers(100)));
    }

    public function testThrowWhenRealNumbersRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        realNumbers(0);
    }

    public function testRange()
    {
        $set = range(-100, 99, 2);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('float', (string) $set->type());
        $this->assertCount(100, $set);
    }

    public function testChar()
    {
        $set = char();

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
        $this->assertCount(1, $set);
        $this->assertSame(1, strlen($set->current()));
    }

    public function testStrings()
    {
        $set = strings(100, 42);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
        $this->assertCount(100, $set);
    }
}
